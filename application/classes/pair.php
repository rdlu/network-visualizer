<?php
/**
 * @author Rodrigo Dlugokenski
 * @copyright PRAV - Inf UFRGS
 * @depends RRD, SNMP, Kohana_Sprig, Kohana_Database
 */

class Pair {

	protected static $instances;

	protected $source;
	protected $destination;
	protected $profiles;
	protected $metrics = array();
	protected $processes = array();
	protected $thresholds = array();
	protected $results = array();
	protected $rrd = null;

	/**
	 * Retorna uma instancia do par origem->destino (nova ou existente)
	 * @static
	 * @param  $sourceId
	 * @param  $destinationId
	 * @param array $options
	 * @return Pair
	 */
	public static function instance($sourceId, $destinationId, $options = array()) {
		$sourceId = (int)$sourceId;
		$destinationId = (int)$destinationId;

		if (!isset(Pair::$instances[$sourceId][$destinationId])) {
			$newinstance = new Pair();
			$newinstance->source = Sprig::factory('entity', array('id' => $sourceId))->load();
			$newinstance->destination = Sprig::factory('entity', array('id' => $destinationId))->load();
			$newinstance->processes = Sprig::factory('process')->load(DB::select()->where('destination_id', '=', $destinationId)->where('source_id', '=', $sourceId), false);

			foreach ($options as $option) {
				$newinstance->$option = $option;
			}
			Pair::$instances[$sourceId][$destinationId] = $newinstance;
		}

		return Pair::$instances[$sourceId][$destinationId];
	}

	public static function instanceFromProcess($processId)
	{
		$process = Sprig::factory('process', array('id' => $processId))->load();
		$id = $process->source->id;
		return self::instance($process->source->id, $process->destination->id);
	}

	public function getSource() {
		return $this->source;
	}

	public function getDestination() {
		return $this->destination;
	}

	public function getMetrics() {
		if (count($this->metrics) == 0) {
			$processes = $this->getProcesses();
			foreach ($processes as $process) {
				$profile = $process->profile->load();
				$metrics = $profile->metrics->as_array();
				foreach ($metrics as $metric) {
					$order[] = $metric->order;
					$this->metrics[] = $metric;
				}
			}
			array_multisort($order, $this->metrics);
		}
		return $this->metrics;
	}

	/**
	 * @return Database_MySQL_Result
	 */
	public function getProcesses($asArray = false) {
		$results = $this->processes;
		//$results must be a Model_results object
		if (is_array($results)) {
			$results = $this->processes = Sprig::factory('process')->load(DB::select()->where('destination_id', '=', $this->destination->id)->where('source_id', '=', $this->source->id), false);
		}

		if ($asArray) {
			$results = array();
			foreach ($this->processes as $process) {
				$results[$process->id] = $process->as_array();
			}
		}

		return $results;
	}

	public function removeProcess(Model_Process $process, $force = false)
	{
		if ($process->loaded()) {
			$source = $process->source->load();
			$destination = $process->destination->load();
			$values = array();

			$sourceSnmp = Snmp::instance($source->ipaddress, 'suppublic');
			//disableAgent
			$sourceSnmp->setGroup('disableAgent', $values, array('id' => $process->id));
			sleep(2);
			//removeAgent
			$sourceSnmp->setGroup('removeAgent', $values, array('id' => $process->id));

			if(!$destination->isAndroid)
                $destinationSnmp = Snmp::instance($destination->ipaddress, 'suppublic')
                    ->setGroup('removeManager', $values, array('id' => $process->id));

			$c = 0;
			$return = 0;
			$c += count($sourceSnmp);
			if ($c > 0) {
				$return = 1;
				//$errors[0] = "A sonda de origem \"$source->name\" ($source->ipaddress) não pode ser contactada para reconfiguração.";
			}

			$d = 0;
			$d += count($destinationSnmp);
			if ($d > 0) {
				$return = 2;
				//$errors[1] = "A sonda de destino \"$destination->name\" ($destination->ipaddress) não pode ser contactada para reconfiguração";
			}

			$c = $c + $d;
			if ($c == 0 || $force) {
				try {
					$process->delete();
				} catch (Database_Exception $e) {
					$return = 4;
				}

			}
		} else {
			$return = 5;
		}

		return $return;
	}

	public function removeProcesses($force = false)
	{
		foreach ($this->getProcesses() as $process) {
			$responses[$process->id] = $this->removeProcess($process, $force);
		}

		$return['errors'] = 0;
		$return['message'] = array("Os processos de medição entre {$this->source->name} ({$this->source->ipaddress}) e {$this->destination->name} ({$this->destination->ipaddress}) foram removidos com sucesso");

		foreach ($responses as $procid => $response) {
			switch ($response) {
				case 0:
					break;
				case 1:
					$return['errors'] += 1;
					$return['message'][$response] = "A sonda de origem \"{$this->source->name}\" ({$this->source->ipaddress}) não pode ser contactada para reconfiguração.";
					break;
				case 2:
					$return['errors'] += 1;
					$return['message'][$response] = "A sonda de destino \"{$this->destination->name}\" ({$this->destination->ipaddress}) não pode ser contactada para reconfiguração";
					break;
				default:
					$return['errors'] += 1;
					$return['message'][$response] = "Erro $response no processo $procid";
					break;
			}
		}

		return $return;
	}

	public function getThresholds()
	{
		if (count($this->thresholds) == 0) {
			foreach ($this->processes as $process) {
				$pthreshold = $process->thresholdProfile->load();
				$pthresholds[$pthreshold->id] = $pthreshold;
			}

			$metrics = $this->getMetrics();
			foreach ($metrics as $metric) {
				$mids[] = $metric->id;
				$meds[$metric->id] = $metric->name;
			}

			$rows = Db::select()->from('thresholdvalues')->or_where('metric_id', 'IN', $mids)->where('thresholdprofile_id', '=', $pthreshold->id)->execute();

			$values = array();
			foreach ($rows as $row) {
				$values[$meds[$row['metric_id']]] = $row;
			}

			$this->thresholds = $values;
		}
		return $this->thresholds;
	}

	/**
	 * @return Rrd
	 */
	public function getRrdInstance() {
		if(!$this->rrd) {
			$this->rrd = Rrd::instance($this->source->ipaddress, $this->destination->ipaddress);
		}
		return $this->rrd;
	}

    public function checkRRDFiles() {
        $rrd = $this->getRrdInstance();
        $returnMessages = array();
        foreach($this->getMetrics() as $metric) {
            if($rrd->isMissingFiles($metric->name)) {
                $rrd->create($metric->name,$metric->profile->load()->polling);
                if($rrd->errors)
                    $returnMessages[] = "Error creating $metric->name RRD Files";
                else
                    $returnMessages[] = "Arquivos RRD para a metrica $metric->name foram recriados.";

            }
        }

        return $returnMessages;
    }

	public function getResult($metric, $start = false, $end = false)
	{
		$rrd = $this->getRrdInstance();
		$last = Date("U");
		//$last = (int) $rrd->last($metric);
		$start = $start ? $start : $last - 600;
		$end = $end ? $end : $last - 300;

		$metricModel = Sprig::factory('metric', array('name' => $metric))->load();


		$json = $rrd->json($metric, $start, $end);

		$obj = Zend_Json::decode($json, Zend_Json::TYPE_OBJECT)->xport;

		$results = new stdClass();
		$results->metric = $metric;
		$results->labels = $obj->meta->legend->entry;
		$results->values = array();

		$mm = array();

		if (count($obj->meta->legend->entry) > 1)
			foreach ($obj->meta->legend->entry as $x => $z) {
				$mm[$x] = explode(' ', $z);
				$mn = $mm[$x][1];
				$results->values[$mn] = array();
			}
		else {
			$results->values['sds'] = array();

		}


		if ($obj->meta->rows > 1) //n linhas
			foreach ($obj->data->row as $k => $row) {
				//n colunas
				if (count($obj->meta->legend->entry) > 1)
					foreach ($row->v as $j => $value) {
						$value = ($value == 'NaN') ? null : $value;
						$mn = $mm[$j][1];
						$af[$row->t] = $value;
						$results->values[$mn] = $af;
					}
					//1 coluna
				else {
					$value = ($row->v == 'NaN') ? null : $row->v;
					$af[$row->t] = $value;
					$results->values['sds'] = $af;

				}
			}
		else {
			//1 linha e n colunas
			if (count($obj->meta->legend->entry) > 1)
				foreach ($obj->data->row->v as $j => $value) {
					$value = ($value == 'NaN') ? null : $value;
					$mn = $mm[$j][1];
					$af[$obj->data->row->t] = $value;
					$results->values[$mn] = $af;
				}
			else { //1linha e 1 coluna
			}
		}

		return $results;
	}

	public function getResults($start = false, $end = false)
	{
		$metrics = $this->getMetrics();
		$results = array('sd' => array(), 'ds' => array());
		foreach ($metrics as $metric) {
			$metricResult = $this->getResult($metric->name, $start, $end);
			if (count($metricResult->values) > 1) {
				$results['sd'][$metric->name] = $metricResult->values['sd'];
				$results['ds'][$metric->name] = $metricResult->values['ds'];
			} else {
				$results['sd'][$metric->name] = $metricResult->values['sds'];
				$results['ds'][$metric->name] = $metricResult->values['sds'];
			}
		}

		$this->results = $results;
		return $results;
	}

	public function lastResults($type = 'sd')
	{
		$results = $this->getResults(false, false);

		$thresholds = $this->getThresholds();

		$return = array();
		foreach ($results[$type] as $metric => $result) {
			//se o ultimo estiver zerado pega o penultimo resultado
			if (end($result) == 0) {
				$return[$metric] = Rrd::sci2num(prev($result));
			} else {
				$return[$metric] = Rrd::sci2num(end($result));
			}
		}

		//Fire::info($return);

		if ($type == 'sd') {
			$target = array(
				'id' => $this->destination->id,
				'ip' => $this->destination->ipaddress,
				'name' => Kohana_UTF8::transliterate_to_ascii($this->destination->name)
			);
		} else {
			$target = array(
				'id' => $this->source->id,
				'ip' => $this->source->ipaddress,
				'name' => Kohana_UTF8::transliterate_to_ascii($this->source->name)
			);
		}

		return array('results' => $return, 'thresholds' => $thresholds, 'target' => $target);
	}

	/*public function setProcesses(array $processes)
	{

	}*/

	/**
	 * Configura um perfil na tabela SNMP do gerente (uma linha no profileTable)
	 * @param $processId
	 * @return bool
	 */
	public function setProfile($processId) {
		$process = Sprig::factory('process',array('id'=>$processId))->load();
		$profile = $process->profile->load();
		$snmp = Snmp::instance($this->source->ipaddress, 'suppublic');

		if ($snmp->isProfileNotLoaded($profile->id)) {
			$values = array('entryStatus' => 6);
			$values = array_merge($values, $profile->as_array());
			$values['gap'] = $profile->gap * 1000;
			$values['metrics'] = $profile->metrics;
			$ptable = $snmp->setGroup('profileTable', $values, array('id' => $profile->id));
			if (count($ptable)) return false;
		}
		return true;
	}

	public function setAgent($processId) {
		$process = Sprig::factory('process',array('id'=>$processId))->load();
		$profile = $process->profile->load();
		$snmp = Snmp::instance($this->source->ipaddress, 'suppublic');

		if ($snmp->isAgentNotLoaded($this->destination->id)) {
			$avalues = array('entryStatus' => 6);
			$avalues = array_merge($avalues, $this->destination->as_array());
			$avalues['profile'] = $profile->id;
			$avalues['port'] = 12000 + $profile->id;
			$avalues['status'] = 1;
			$atable = $snmp->setGroup('agentTable', $avalues, array('pid' => $process->id));
			if(count($atable)) return false;
		}

		return true;
	}

	protected function setManagerTable() {

	}
	
}
