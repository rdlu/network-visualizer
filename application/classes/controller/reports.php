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
		if(Request::$is_ajax) {
			//$this->auto_render = false;
			$this->template = View::factory('templates/empty');
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

	   $processes = Sprig::factory('process',array('destination_id'=>$dId,'source_id'=>$sId))->load(NULL,false);
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
					$img = $rrd->graph($profile->id,$metric->name,$inicio,$fim);
				}
			}
		} else {
			$this->template->content = "Não há nenhum processo de medição entre $source->name ($source->ipaddress) e $destination->name ($destination->ipaddress)";
		}
	   Fire::groupEnd();
	}

}
