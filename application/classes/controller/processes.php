<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Processes extends Controller_Skeleton {

	public function before() {
		parent::before();
		$this->template->title .= 'Processos de Medição :: ';
	}

	public function action_index() {
		$view = View::factory('processes/index');
		$this->template->title .= 'Escolha da Entidade de Origem do Teste';

		$entities = Sprig::factory('entity')->load(NULL, FALSE);
		$estados = Sprig::factory('uf')->load(NULL, FALSE);
		$view->bind('entities', $entities);
		$this->template->content = $view;
	}

	public function action_list($sourceAddr = 0) {
		$this->auto_render = false;
		$view = View::factory('processes/list');
		$this->template->title .= 'Escolha da Entidade de Destino do Teste';

		if (!$sourceAddr) {
			$sourceAddr = '128.0.0.1';
			$errors[] = "Selecione uma sonda na lista.";
		}

		if (Valid::ipOrHostname($sourceAddr)) {
			$sourceEntity = Sprig::factory('entity', array('ipaddress' => $sourceAddr))->load();
			$processes = Sprig::factory('process', array('source' => $sourceEntity->id))->load(NULL, FALSE);

			foreach($processes as $process) {
				$destination = $process->destination->load();
				$destinations[$destination->id] = $destination;
				$pair = Pair::instance($sourceEntity->id,$destination->id);
				$metrics[$destination->id] = $pair->getMetrics();
			}

			Fire::group('Models Loaded')
					->info($processes)
					->info($sourceEntity)
					//->info($destinations)
					->groupEnd();
		} else {
			$errors[] = "A origem $sourceAddr não é um IP válido. Você deve informar um IP válido.";
		}


		$view->bind('processes', $processes)
				->bind('errors', $errors)
				->bind('source', $sourceEntity)
				->bind('sourceAddr', $sourceAddr)
				->bind('metrics',$metrics)
				->bind('destinations', $destinations);
		$this->response->body($view);
	}

	/**
	 * SETUP de NOVO PROCESSO
	 * O Setup de um novo processo é feito em 3 partes
	 */
	/**
	 * Funcao que mostra o formulario de escolha de destino, baseado na origem.
	 * @param int $source
	 * @return void
	 */
	public function action_new($source = 0) {
		$this->template->title .= 'Criando novo processo de medição';
		$process = Sprig::factory('process');
		$profiles = Sprig::factory('profile')->load(NULL, FALSE);
		$thresholds = Sprig::factory('thresholdProfile')->load(NULL, FALSE);

		if ($source != 0) {
			$sourceEnt = Sprig::factory('entity', array('id' => $source))->load();
			$process->source = $sourceEnt->id;
			$sourceEntity = $process->source->load();
			Fire::group('Models Loaded', array('Collapsed' => 'true'))->info($process)->info($sourceEntity)->groupEnd();
		}

		$db = DB::select()->where('profile_id', 'IS NOT', null)->order_by('order');

		$metrics = Sprig::factory('metric')->load($db, false);

		if ($_POST) {
			try {
				$process->check($_POST);
				$this->request->redirect($this->request->controller . '/setup/' . $process->source->id);
			} catch (Validation_Exception $e) {
				$errors = $e->array->errors('processes/edit');
				Fire::group('Form Validation Results')->warn($errors)->groupEnd();
			}
		}

		$measure = Kohana::config('measure');
		$conversion = new conversion();

		foreach($thresholds as $threshold) {
			$tvalues = $threshold->thresholdValues;
			$tarr = array();
			foreach($tvalues as $tvalue) {
				$tmet = $tvalue->metric->load();
				$tarr[$tmet->name] = $tvalue->as_array();
				$tarr[$tmet->name]['reverse'] = (int) $tmet->reverse;
				$type = $measure[$tmet->name]['type'];

				//Faz a conversao dos valores em bps/segundos para um formato mais humano (Kbps/Mbps/ms)
				if($type) {
					$tarr[$tmet->name]['max'] = $conversion->{$type}($tarr[$tmet->name]['max']);
					$tarr[$tmet->name]['min'] = $conversion->{$type}($tarr[$tmet->name]['min']);
				} else {
					//Caso nao precise de conversao só concatena a unidade de medida
					$tarr[$tmet->name]['max'] = $tarr[$tmet->name]['max']." ".$measure[$tmet->name]['default'];
					$tarr[$tmet->name]['min'] = $tarr[$tmet->name]['min']." ".$measure[$tmet->name]['default'];
				}

			}
			$tvals[$threshold->id] =$tarr;
		}
		
		$view = View::factory('processes/form');
		$view->bind('process', $process)
				->bind('errors', $errors)
				->bind('source', $source)
				->bind('sourceEntity', $sourceEntity)
				->bind('profiles', $profiles)
				->bind('thresholds',$thresholds)
				->bind('thresholdsValues',$tvals)
				->bind('metrics', $metrics);
		$this->template->content = $view;
	}

	/**
	 * Funcao que inicializa o setup do processo, apos o formulario ser enviado
	 * @param  $id
	 * @return void
	 */
	public function action_setup($source) {
		$this->template->title .= 'Configurando o novo processo de medição';
		//validando dados
		$validado = false;
		if (Valid::numeric($_POST['destination']) && ($_POST['destination'] > 0)) $validado = true;
		$view = View::factory('processes/setup');

		if ($validado) {
			$source = (int) $source;
			$destination = (int) $_POST['destination'];
			$metrics = (array) $_POST['metrics'];
			$thresholdProfile = (int) $_POST['threshold'];

			$rows = DB::select()->from('metrics')->group_by('profile_id')->where('profile_id',"!=",null)->or_where('id', 'IN', $metrics)->execute();

			$profiles = array();
			foreach ($rows as $row) {
				$profiles[] = $row['profile_id'];
			}

			$sourceModel = Sprig::factory('entity', array('id' => $source))->load();
			$destinationModel = Sprig::factory('entity', array('id' => $destination))->load();
			$thresholdModel = Sprig::factory('thresholdProfile', array('id' => $thresholdProfile))->load();

			$errors = false;

			foreach ($profiles as $k => $profile) {
				$process = Sprig::factory('process');
				$process->source = $sourceModel;
				$process->destination = $destinationModel;
				$process->profile = $profile;
				$process->metrics = $metrics;
				$process->thresholdProfile = $thresholdModel;

				try {
					$process->create();
					$resultModels[$k] = $process->load();
					$resultIds[$k] = $resultModels[$k]->id;
				} catch (Exception $e) {
					$msg = $e->getMessage();
					$errors[$k]['message'] = "Exceção no banco de dados, $msg";
					$errors[$k]['class'] = 'error';
				}

			}

			$view->bind('process', $process)->bind('processes',$resultModels)->bind('processIDs',$resultIds)
					->bind('destination', $destinationModel)
					->bind('source', $sourceModel)
					->bind('profiles', $profiles)->bind('errors', $errors);
		} else {
			throw new Exception('Validation error on Process Setup, non numeric data sent', 23);
		}
		$this->template->content = $view;
	}

	public function action_setupDestination() {
		if (Request::current()->is_ajax()) {
			$this->auto_render = false;
			$procs = (array) $_POST['processes'];

			foreach ($procs as $i => $proc) {
				$process = Sprig::factory('process',array('id'=>$proc))->load();
				Fire::info($process->as_array(),"Process $i to be configured. ID: $proc");
				$source = $process->source->load();
				$destination = $process->destination->load();
				$profile = $process->profile->load();
				$values = array(
					'managerEntryStatus' => 6,
					'managerAddress' => $source->ipaddress,
					'managerPort' => 12000 + $profile->id,
					'managerProtocol' => $profile->protocol
				);

				Fire::info($values,"To do on Manager Table");

				$snmp = Snmp::instance($destination->ipaddress, 'suppublic')->setGroup('managerTable', $values, array('id' => $process->id));

				if (count($snmp)) {
					$e['errors'] = $snmp;
					Kohana::$log->add(Log::ERROR, "Erro no SNMP set managerTable para o ip $destination->ipaddress");
					$e['message'] = 'Erros na transmissão dos dados via SNMP: ';
					$e['class'] = 'error';
					break;
				} else {
					$e['errors'] = null;
					$e['message'] = "Entidade de destino $destination->name ($destination->ipaddress) foi configurada com sucesso via SNMP.";
					$e['class'] = 'success';
				}

			}


			$this->response->headers('Content-Type', 'application/json');
			$this->response->body(json_encode($e));
		}
	}


	public function action_setupSource() {
		if (Request::current()->is_ajax()) {
			$this->auto_render = false;
			$procs = (array) $_POST['processes'];

			foreach ($procs as $i => $proc) {
				$process = Sprig::factory('process',array('id'=>$proc))->load();
				Fire::info($process->as_array(),"Process $i to be configured. ID: $proc");
				$source = $process->source->load();
				$destination = $process->destination->load();
				$profile = $process->profile->load();
				//Checar se o destino esta OK
				$sourceSnmp = Snmp::instance($source->ipaddress, 'suppublic');

				if (!$sourceSnmp->isReachable(NMMIB . '.1.0.9.' . $process->id)) {
					$values = array('entryStatus' => 6);
					$values = array_merge($values, $profile->as_array());
					$values['gap'] = $profile->gap * 1000;
					$values['metrics'] = $profile->metrics;
					$ptable = $sourceSnmp->setGroup('profileTable', $values, array('id' => $profile->id));
				} else {
					$ptable = array();
				}

				$avalues = array('entryStatus' => 6);
				$avalues = array_merge($avalues, $destination->as_array());
				$avalues['profile'] = $profile->id;
				$avalues['port'] = 12000 + $profile->id;
				$avalues['status'] = 1;
				$atable = $sourceSnmp->setGroup('agentTable', $avalues, array('pid' => $process->id));

				if (count($ptable)) {
					$e['errors'] = array_keys($ptable);
					Kohana::$log->add(Log::ERROR, "Erro no SNMP set Source para o ip $source->ipaddress (Profile Table)");
					$e['message'] = 'Erros na transmissão dos dados via SNMP (Profile Setup)';
					$e['class'] = 'error';
					$ptrue = true;
				}

				if (count($atable)) {
					Kohana::$log->add(Log::ERROR, "Erro no SNMP set Source para o ip $source->ipaddress (Agent Table)");
					$e['message'] = 'Erros na transmissão dos dados via SNMP (Agent Setup)';
					$e['class'] = 'error';
					if (isset($ptrue)) {
						$e['message'] .= ' & (Profile Setup)';
						$e['errors'] = array_merge($e['errors'], array_keys($ptable));
					} else
						$e['errors'] = array_keys((array) $ptable);
					$atrue = true;
				}

				if (!isset($ptrue) && !isset($atrue)) {
					$e['errors'] = null;
					$e['class'] = 'success';
					$e['message'] = "Entidade de origem $source->name ($source->ipaddress) foi configurada com sucesso via SNMP.";
				} else
					break;
			}

			$this->response->headers('Content-Type', 'application/json');
			$this->response->body(json_encode($e));
		}
	}

	public function action_FinalCheck() {
		if (Request::current()->is_ajax()) {
			$this->auto_render = false;
			$procs = (array) $_POST['processes'];

			foreach ($procs as $i => $proc) {
				$process = Sprig::factory('process',array('id'=>$proc))->load();
				Fire::info($process->as_array(),"Process $i to be configured. ID: $proc");
				$source = $process->source->load();
				$destination = $process->destination->load();

				$profile = $process->profile->load();
				if (Snmp::instance($source->ipaddress)->isReachable(NMMIB . '.0.0.0.' . $process->id)) {
					if (Snmp::instance($destination->ipaddress)->isReachable(NMMIB . '.10.0.0.' . $process->id)) {
						$source->status = 1; $source->update();
						$destination->status = 2; $destination->update();
						$response['message'] = "Configurações salvas com sucesso no banco de dados do MoM";
						$response['class'] = 'success';
						$rrd = Rrd::instance($source->ipaddress, $destination->ipaddress);

						foreach ($profile->metrics as $metric) {
							$rrd->create($metric->name, $profile->polling);
						}

						if ($rrd->errors) {
							$response['message'] .= ', mas houveram falhas na criação dos arquivos RRD, cheque o Registro de Eventos.';
							$response['class'] = 'warn';
						}
					} else {
						$response['message'] = "A sonda de destino $destination->ipaddress não respondeu ao teste de verificação, abortando a configuração";
						$response['class'] = 'error';
						$values['entryStatus'] = 2;
						$sourceSnmp = Snmp::instance($source->ipaddress, 'suppublic')->setGroup('removeAgent', $values, array('id' => $process->id));
						$process->delete();
						//break;
					}
				} else {
					$response['message'] = "A sonda de origem $source->ipaddress não respondeu ao teste de verificação, abortando a configuração.";
					$response['class'] = 'error';
					$process->delete();
					//break;
				}
			}



			$this->response->headers('Content-Type', 'application/json');
			$this->response->body(json_encode($response));

		}
	}

	public function action_remove() {
		if (Request::current()->is_ajax()) {
			$this->auto_render = false;

			$source = (int) $_POST['source'];
			$destination = (int) $_POST['destination'];
			$force = (boolean) (isset($_POST['force'])) ? $_POST['force'] : false;

			$pair = Pair::instance($source,$destination);
			$responses = $pair->removeProcesses($force);

			//procura se eh a ultima medicao removida no destino
			$db = Db::select()->from('processes')->or_where('source_id','=',$destination)->or_where('destination_id','=',$destination)->execute();

			if($db->count() == 0) {
				$sou = Sprig::factory('entity',array('id'=>$destination))->load();
				$sou->status = 0;
				$sou->update();
			}

			$this->response->headers('Content-Type', 'application/json');
			$this->response->body(json_encode($responses));
		}
	}

	public function action_view($id) {
		$id = (int) $id;
		$view = View::factory('process/view');

		$entity = Sprig::factory('entity', array('id' => $id))->load();

		if ($entity->loaded()) {
			$view->bind('entity', $entity);
			$this->template->content = $view;
		} else {
			$this->template->content = 'Entidade não localizada no sistema';
		}
	}

} // End Welcome
