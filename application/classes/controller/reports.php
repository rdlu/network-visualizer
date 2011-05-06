<?php
/**
 *
 * @author Rodrigo Dlugokenski
 */
class Controller_Reports extends Controller_Skeleton {

	public function before() {
		if($this->auto_render) {
			parent::before();
			$this->template->title .= "Relatórios :: ";
		}
	}
	public function action_Index() {
		$view = View::factory('reports/index');

      $entities = Sprig::factory('entity')->load(NULL, FALSE);
      $estados = Sprig::factory('uf')->load(NULL, FALSE);
      $view->bind('entities',$entities);
      $this->template->content = $view;
	}

	public function action_View($sId=0,$dId=0,$start=0,$end=0,$stime=0,$etime=0) {
		$sId = (int) $sId;
		$dId = (int) $dId;
		$view = View::factory('reports/view');
		if(Request::current()->is_ajax()) {
			$this->auto_render = false;
			$sId = (int) $_POST['source'];
		   $dId = (int) $_POST['destination'];

			$relative = (isset($_POST['relative']))?$_POST['relative']:false;

			if($relative) {
				$inicio = strtotime($relative);
				$end = Date("U");
			} else {
				$start = $_POST['startDate'];
		      $end = $_POST['endDate'];
		      $stime = $_POST['startHour'];
		      $etime = $_POST['endHour'];
				$inicio = Rrd::converteData($start)." ".$stime;
				$fim = Rrd::converteData($end)." ".$etime;
			}

		}

	   //Valida dados
		if(!Valid::data($start)) throw new Kohana_Exception("Start Date not valid",array($start));
	   if(!Valid::data($end)) throw new Kohana_Exception("End Date not valid",array($end));
	   if(!Valid::hora($stime)) throw new Kohana_Exception("Start time not valid",array($stime));
	   if(!Valid::hora($etime)) throw new Kohana_Exception("End time not valid",array($etime));

	   $processes = Sprig::factory('process')->load(DB::select()->where('destination_id','=',$dId)->where('source_id','=',$sId),false);
	   $source = Sprig::factory('entity',array('id'=>$sId))->load();
	   $destination = Sprig::factory('entity',array('id'=>$dId))->load();

	   //Gerando os graficos
		$rrd = Rrd::instance($source->ipaddress,$destination->ipaddress);

		$count = $processes->count();
		
		Fire::group("Report status for $source->ipaddress to $destination->ipaddress",array('Collapsed'=>'true'))->info("Number of processes: $count");

		if($count) {

			$metrics2 = array();

			foreach($processes as $process) {
				Fire::info($process->as_array(),"Process 1, ID: $process->id");
				$profile = $process->profile->load();
				$metrics = $process->metrics->as_array('order');
				ksort($metrics);
				Fire::error($metrics);
				foreach($metrics as $metric) {
					$img[$metric->name] = $rrd->graph($metric->name,$inicio,$fim);
				}
			}

		   Fire::group("Images path")->info($img)->groupEnd();
		   Fire::groupEnd();

		   if(Request::current()->is_ajax()) {
			   $view->bind('images',$img)->bind('metrics',$metrics)
			      ->bind('processes',$processes);
			   $this->response->headers('Cache-Control',"no-cache");
			   $this->response->body($view);
		   } else {
			   $this->template->content = $view;
		   }



		} else {
			$this->template->content = "Não há nenhum processo de medição entre $source->name ($source->ipaddress) e $destination->name ($destination->ipaddress)";
		}
	}

	public function action_xml($pId=0,$metric,$start='25/01/2011',$end='25/01/2011',$stime='13:00',$etime='14:00') {
		if(true || Request::current()->is_ajax()) {
			$this->auto_render = false;
			/*$pId = (int) $_POST['processId'];
			$start = $_POST['startDate'];
		   $end = $_POST['endDate'];
		   $stime = $_POST['startHour'];
		   $etime = $_POST['endHour'];
		   $metric = $_POST['metric'];*/

			//Valida dados
			if(!Valid::data($start)) throw new Kohana_Exception("Start Date not valid",array($start));
			if(!Valid::data($end)) throw new Kohana_Exception("End Date not valid",array($end));
			if(!Valid::hora($stime)) throw new Kohana_Exception("Start time not valid",array($stime));
			if(!Valid::hora($etime)) throw new Kohana_Exception("End time not valid",array($etime));

		   $process = Sprig::factory('process',array('id'=>$pId))->load();

		   if($process->count()) {
			   $source = $process->source->load();
		      $destination = $process->destination->load();
		      $profile = $process->profile->load();
			   $rrd = Rrd::instance($source->ipaddress,$destination->ipaddress);
			   $inicio = Rrd::converteData($start)." ".$stime;
				$fim = Rrd::converteData($end)." ".$etime;
				$xml = $rrd->xml($profile->id,$metric,$inicio,$fim);
		      $this->response->body(Zend_Json::fromXml($xml));

		   } else {
			   throw new Kohana_Exception("Processo $pId nao encontrado");
		   }

		} else throw new Kohana_Exception("Essa ação somente responde requisições AJAX");
	}

	public function action_View2($sId=0,$dId=0,$start=0,$end=0,$stime=0,$etime=0) {
		$sId = (int) $sId;
		$dId = (int) $dId;
		$view = View::factory('reports/view');
		if(Request::current()->is_ajax()) {
			$this->auto_render = false;
			$sId = (int) $_POST['source'];
		   $dId = (int) $_POST['destination'];
			$start = $_POST['startDate'];
		   $end = $_POST['endDate'];
		   $stime = $_POST['startHour'];
		   $etime = $_POST['endHour'];
		}

	   //Valida dados
		if(!Valid::data($start)) throw new Kohana_Exception("Start Date not valid",array($start));
	   if(!Valid::data($end)) throw new Kohana_Exception("End Date not valid",array($end));
	   if(!Valid::hora($stime)) throw new Kohana_Exception("Start time not valid",array($stime));
	   if(!Valid::hora($etime)) throw new Kohana_Exception("End time not valid",array($etime));

	   $processes = Sprig::factory('process')->load(DB::select()->where('destination_id','=',$dId)->where('source_id','=',$sId),false);
	   //array('destination_id'=>$dId,'source_id'=>$sId)
	   $source = Sprig::factory('entity',array('id'=>$sId))->load();
	   $destination = Sprig::factory('entity',array('id'=>$dId))->load();

	   //Gerando os graficos
		$rrd = Rrd::instance($source->ipaddress,$destination->ipaddress);

		$count = $processes->count();

		Fire::group("Report status for $source->ipaddress to $destination->ipaddress",array('Collapsed'=>'true'))->info("Number of processes: $count");

		if($count) {
			$inicio = Rrd::converteData($start)." ".$stime;
			$fim = Rrd::converteData($end)." ".$etime;

			foreach($processes as $process) {
				Fire::info($process->as_array(),"Process 1, ID: $process->id");
				$profile = $process->profile->load();
				$metrics = $profile->metrics;
				foreach($metrics as $metric) {
					$img[$profile->id][$metric->name] = $rrd->graph($profile->id,$metric->name,$inicio,$fim);
				}
			}

		   Fire::group("Images path")->info($img)->groupEnd();
		   Fire::groupEnd();

		   if(Request::current()->is_ajax()) {
			   $view->bind('images',$img)
			      ->bind('processes',$processes);
			   $this->response->body($view);
		   } else {
			   $this->template->content = $view;
		   }



		} else {
			$this->template->content = "Não há nenhum processo de medição entre $source->name ($source->ipaddress) e $destination->name ($destination->ipaddress)";
		}
	}

	public function action_json() {
		$this->auto_render = false;

		/*$source = $_POST['source'];
		$destination = $_POST['destination'];
		$metric = $_POST['metric'];*/
		$source=5;$destination=8;$metric='capacity';

		$pair = Pair::instance($source,$destination);

//		$this->response->headers('Content-Type','application/json');
		$this->response->body(Zend_Json::encode($pair->getResult($metric)));
	}

	public function action_flot() {
		$this->auto_render = false;
		$source = $_POST['source'];
		$destination = $_POST['destination'];
		$metric = $_POST['metric'];
		//$source='143.54.10.199';$destination='143.54.10.122';$metric='owd';

		$start = (isset($_POST['start']))?$_POST['start']:date("U", time() - 24*3600);
		$end = (isset($_POST['end']))?$_POST['end']:date("U");

		$metricModel = Sprig::factory('metric',array('name'=>$metric))->load();
		$profile = $metricModel->profiles->current();

		$json = Rrd::instance($source,$destination)->json($metric,$start,$end);

		$obj = Zend_Json::decode($json,Zend_Json::TYPE_OBJECT)->xport;

		$results = new stdClass();
		$results->labels = $obj->meta->legend->entry;
		$results->values = new stdClass();

		foreach($obj->meta->legend->entry as $x => $z) {
			$results->values->$x = array();
		}

		foreach($obj->data->row as $k => $row) {
			foreach($row->v as $j => $value) {
				$value = ($value == 'NaN') ? null:$value;
				array_push($results->values->$j,array($row->t,$value));
			}
		}

		$this->response->headers('Content-Type','application/json');
		$this->response->body(Zend_Json::encode($results));
	}

	public function action_lastResultsFromPair($source,$destination) {
		$this->auto_render = false;

		//$source = $_POST['source'];
		//$destination = $_POST['destination'];

		$pair = Pair::instance($source,$destination);

		if(Request::current()->is_ajax()) $this->response->headers('Content-Type','application/json');
		$this->response->body(Zend_Json::encode($pair->lastResults()));
	}
//Rodrigo, se possível, altera essa função sempre em synthesizing - action_destsondas. Vlw
	public function action_lastResultsFromSource($source) {
		$this->auto_render = false;

		//$source = $_POST['source'];

		$source = Sprig::factory('entity',array('id'=>$source))->load();
		$processes = Sprig::factory('process')->load(Db::select()->group_by('destination_id')->where('source_id','=',$source->id),null);
		$resp = array();
		foreach($processes as $process) {
			$resp[] = $process->destination->load();
		}

		foreach($resp as $destination) {
			$pair = Pair::instance($source->id,$destination->id);
			$resultss[] = $pair->lastResults();
		}

		if(Request::current()->is_ajax()) $this->response->headers('Content-Type','application/json');
		$this->response->body(Zend_Json::encode($resultss));
	}
}
