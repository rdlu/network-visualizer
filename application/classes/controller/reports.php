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
		if(Request::$is_ajax) {
			$this->auto_render = false;
			$sId = (int) $_POST['source'];
		   $dId = (int) $_POST['destination'];
			$start = $_POST['startDate'];
		   $end = $_POST['endDate'];
		   $stime = $_POST['startHour'];
		   $etime = $_POST['endHour'];
		}

	   //Valida dados
		if(!Validate::data($start)) throw new Kohana_Exception("Start Date not valid",array($start));
	   if(!Validate::data($end)) throw new Kohana_Exception("End Date not valid",array($end));
	   if(!Validate::hora($stime)) throw new Kohana_Exception("Start time not valid",array($stime));
	   if(!Validate::hora($etime)) throw new Kohana_Exception("End time not valid",array($etime));

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

		   if(Request::$is_ajax) {
			   $view->bind('images',$img)
			      ->bind('processes',$processes);
			   $this->request->headers['Cache-Control'] = "no-cache";
			   $this->request->response = $view;
		   } else {
			   $this->template->content = $view;
		   }



		} else {
			$this->template->content = "Não há nenhum processo de medição entre $source->name ($source->ipaddress) e $destination->name ($destination->ipaddress)";
		}
	}

	public function action_xml($pId=0,$metric,$start='25/01/2011',$end='25/01/2011',$stime='13:00',$etime='14:00') {
		if(true || Request::$is_ajax) {
			$this->auto_render = false;
			/*$pId = (int) $_POST['processId'];
			$start = $_POST['startDate'];
		   $end = $_POST['endDate'];
		   $stime = $_POST['startHour'];
		   $etime = $_POST['endHour'];
		   $metric = $_POST['metric'];*/

			//Valida dados
			if(!Validate::data($start)) throw new Kohana_Exception("Start Date not valid",array($start));
			if(!Validate::data($end)) throw new Kohana_Exception("End Date not valid",array($end));
			if(!Validate::hora($stime)) throw new Kohana_Exception("Start time not valid",array($stime));
			if(!Validate::hora($etime)) throw new Kohana_Exception("End time not valid",array($etime));

		   $process = Sprig::factory('process',array('id'=>$pId))->load();

		   if($process->count()) {
			   $source = $process->source->load();
		      $destination = $process->destination->load();
		      $profile = $process->profile->load();
			   $rrd = Rrd::instance($source->ipaddress,$destination->ipaddress);
			   $inicio = Rrd::converteData($start)." ".$stime;
				$fim = Rrd::converteData($end)." ".$etime;
				$xml = $rrd->xml($profile->id,$metric,$inicio,$fim);
		      $this->request->response = Zend_Json::fromXml($xml);

		   } else {
			   throw new Kohana_Exception("Processo $pId nao encontrado");
		   }

		} else throw new Kohana_Exception("Essa ação somente responde requisições AJAX");
	}

	public function action_View2($sId=0,$dId=0,$start=0,$end=0,$stime=0,$etime=0) {
		$sId = (int) $sId;
		$dId = (int) $dId;
		$view = View::factory('reports/view');
		if(Request::$is_ajax) {
			$this->auto_render = false;
			$sId = (int) $_POST['source'];
		   $dId = (int) $_POST['destination'];
			$start = $_POST['startDate'];
		   $end = $_POST['endDate'];
		   $stime = $_POST['startHour'];
		   $etime = $_POST['endHour'];
		}

	   //Valida dados
		if(!Validate::data($start)) throw new Kohana_Exception("Start Date not valid",array($start));
	   if(!Validate::data($end)) throw new Kohana_Exception("End Date not valid",array($end));
	   if(!Validate::hora($stime)) throw new Kohana_Exception("Start time not valid",array($stime));
	   if(!Validate::hora($etime)) throw new Kohana_Exception("End time not valid",array($etime));

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

		   if(Request::$is_ajax) {
			   $view->bind('images',$img)
			      ->bind('processes',$processes);
			   $this->request->response = $view;
		   } else {
			   $this->template->content = $view;
		   }



		} else {
			$this->template->content = "Não há nenhum processo de medição entre $source->name ($source->ipaddress) e $destination->name ($destination->ipaddress)";
		}
	}

}
