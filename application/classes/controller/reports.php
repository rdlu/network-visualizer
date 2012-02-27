<?php
/**
 *
 * @author Rodrigo Dlugokenski
 */
class Controller_Reports extends Controller_Skeleton {

	public $auth_required = array('login');

	// Controls access for separate actions
	// 'adminpanel' => 'admin' will only allow users with the role admin to access action_adminpanel
	// 'moderatorpanel' => array('login', 'moderator') will only allow users with the roles login and moderator to access action_moderatorpanel
	public $secure_actions = FALSE;

	public function before() {
		if($this->auto_render) {
			parent::before();
			$this->template->title .= "Relatórios :: ";

			$scripts = array(
				'js/flot/jquery.flot.min.js',
				'js/flot/jquery.flot.crosshair.js',
				'js/flot/jquery.flot.selection.js',
				'js/flot/jquery.flot.resize.js',
				'js/dev/conversion.js',
				'js/dev/utils.js'
			);

			$this->template->scripts = array_merge($scripts,$this->template->scripts);
		}
	}

	public function action_Index() {
		$view = View::factory('reports/index');

      $entities = Sprig::factory('entity')->load(NULL, FALSE);
      $view->bind('entities',$entities)
         ->set('defaultManager',Sonda::getDefaultManager());
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
		
		//Fire::group("Report status for $source->ipaddress to $destination->ipaddress",array('Collapsed'=>'true'))->info("Number of processes: $count");

		if($count) {

			$metrics2 = array();

			foreach($processes as $process) {
				//Fire::info($process->as_array(),"Process 1, ID: $process->id");
				$profile = $process->profile->load();
				$metrics = $process->metrics->as_array('order');
				ksort($metrics);
				//Fire::error($metrics);
				foreach($metrics as $metric) {
					$img[$metric->name] = $rrd->graph($metric->name,$inicio,$fim);
				}
			}

		   //Fire::group("Images path")->info($img)->groupEnd();
		   //Fire::groupEnd();

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

	public function action_ViewFlot($sId=0,$dId=0,$start=0,$end=0,$stime=0,$etime=0) {
		$sId = (int) $sId;
		$dId = (int) $dId;
		$view = View::factory('reports/viewFlot');
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

		$count = $processes->count();

		if($count) {

			foreach($processes as $process) {
				$profile = $process->profile->load();
				$metrics = $process->metrics->as_array('order');
				ksort($metrics);
				//Fire::error($metrics);
				foreach($metrics as $metric) {
					$flot[$metric->name] = $this->singleFlot($source->id,$destination->id,$metric->name,$inicio,$fim,1000,true);
				}
			}

		   if(Request::current()->is_ajax()) {
			   $view->set('results',Zend_Json::encode($flot))
			         ->bind('startDate',$start)
			         ->bind('endDate',$end)
			         ->bind('startHour',$stime)
			         ->bind('endHour',$etime)
					   ->bind('metrics',$metrics)
			         ->bind('processes',$processes)
					   ->set('source',$source->as_array())
					   ->set('destination',$destination->as_array());
			   $this->response->headers('Cache-Control',"no-cache");
			   $this->response->body($view);
		   } else {
			   $this->template->content = $view;
		   }



		} else {
			$this->template->content = "Não há nenhum processo de medição entre $source->name ($source->ipaddress) e $destination->name ($destination->ipaddress)";
		}
	}

    public function action_viewXport($sId=0,$dId=0) {
        $sId = (int) $sId;
        $dId = (int) $dId;
        $view = View::factory('reports/viewXport');
        if(Request::current()->is_ajax()) {
            $this->auto_render = false;
            $sId = (int) $_POST['source'];
            $dId = (int) $_POST['destination'];
        }

        $processes = Sprig::factory('process')->load(DB::select()->where('destination_id','=',$dId)->where('source_id','=',$sId),false);
        $source = Sprig::factory('entity',array('id'=>$sId))->load();
        $destination = Sprig::factory('entity',array('id'=>$dId))->load();

        $count = $processes->count();

        if($count) {

            foreach($processes as $process) {
                $profile = $process->profile->load();
                $metrics = $process->metrics->as_array('order');
                ksort($metrics);
            }

            if(Request::current()->is_ajax()) {
                $view->bind('metrics',$metrics)
                    ->bind('processes',$processes)
                    ->set('source',$source->as_array())
                    ->set('destination',$destination->as_array());
                $this->response->headers('Cache-Control',"no-cache");
                $this->response->body($view);
            } else {
                $this->template->content = $view;
            }

        } else {
            $this->template->content = "Não há nenhum processo de medição entre $source->name ($source->ipaddress) e $destination->name ($destination->ipaddress)";
        }

    }

    public function action_xport() {
        $this->auto_render = false;
        $startDate = $_POST['startDate'];
        $startHour = $_POST['startHour'];
        $endDate = $_POST['endDate'];
        $endHour = $_POST['endHour'];
        $metricId = $_POST['metric'];
        $sourceId = (int) $_POST['source'];
        $destinationId = (int) $_POST['destination'];

        $source = Sprig::factory('entity',array('id'=>$sourceId))->load();
        $destination = Sprig::factory('entity',array('id'=>$destinationId))->load();
        $metric = Sprig::factory('metric',array('id'=>$metricId))->load();
        $profile = $metric->profile->load();
        $process = Sprig::factory('process')->load(
            DB::select()->where('destination_id','=',$destinationId)
                ->where('source_id','=',$sourceId)
                ->where('profile_id','=',$profile->id)
        );

        $filename = "momreport-".$destination->name."-".$metric->name."-".$startDate.$startHour."-".$endDate.$endHour;

        list($day, $month, $year) = explode('/', $startDate);
        list($hour, $minutes) = explode(':', $startHour);
        $startTimestamp = mktime($hour, $minutes, 0, $month, $day, $year);
        $startSQLTimestamp = $year."-".$month."-".$day." ".$startHour;

        list($day, $month, $year) = explode('/', $endDate);
        list($hour, $minutes) = explode(':', $endHour);
        $endTimestamp = mktime($hour, $minutes, 0, $month, $day, $year);
        $endSQLTimestamp = $year."-".$month."-".$day." ".$endHour;

        $results = Model_Results::factory($profile->id,$metric->id)->query($process->id,$startSQLTimestamp,$endSQLTimestamp);

        $separator = ";";
        $values = array(
            "timestamp"=>"Horario",
            "dsavg"=>"Up (Avg)",
            "sdavg"=>"Down (Avg)",
            "dsmin"=>"Up (Min)",
            "sdmin"=>"Down (Min)",
            "dsmax"=>"Up (Max)",
            "sdmax"=>"Down (Max)"
        );
        $header = implode($separator,$values);
        $body = "";

        foreach($results as $result) {
            $line = "";
            foreach($values as $column => $name) {
                $line .= str_replace(".",",",$result[$column]).$separator;
            }
            $body .= $line."\r\n";
        }

        $this->response->headers("Content-Disposition", "attachment;filename=$filename.csv");
        $this->response->headers('Content-Type','text/csv');

        $this->response->body($header."\r\n".$body);
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

		//Fire::group("Report status for $source->ipaddress to $destination->ipaddress",array('Collapsed'=>'true'))->info("Number of processes: $count");

		if($count) {
			$inicio = Rrd::converteData($start)." ".$stime;
			$fim = Rrd::converteData($end)." ".$etime;

			foreach($processes as $process) {
				//Fire::info($process->as_array(),"Process 1, ID: $process->id");
				$profile = $process->profile->load();
				$metrics = $profile->metrics;
				foreach($metrics as $metric) {
					$img[$profile->id][$metric->name] = $rrd->graph($profile->id,$metric->name,$inicio,$fim);
				}
			}

		   //Fire::group("Images path")->info($img)->groupEnd();
		   //Fire::groupEnd();

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

    protected function singleCSV($source,$destination,$metric,$start=false,$end=false,$multiplier=1,$timeOffset=false) {
        $start = ($start)?$start:date("U", time() - 3600);
        $end = ($end)?$end:date("U");
        $pair = Pair::instance($source,$destination);
        $c = ";";

        //$types = array('Avg','Min','Max');
        $type = 'Avg';
        $line = "\r\n";
        $exportString = $metric.$c.$source.$c.$destination.$c.$start.$c.$end.$line;

        //foreach($types as $type) {
            $json = $pair->getRrdInstance()->json($metric,$start,$end,$type);
            $obj = Zend_Json::decode($json,Zend_Json::TYPE_OBJECT)->xport;
            

            if(is_array($obj->meta->legend->entry)) {
                foreach($obj->meta->legend->entry as $x => $z) {
                    $entries[0][$x] = $z;
                }
            } else {
                $results->labels = array($obj->meta->legend->entry);
                $rrdlabel = explode(" ",$obj->meta->legend->entry);
                $direction[0] = $rrdlabel[1];
                $values[$direction[0]] = null;
                $results->values = $values;
            }

            $now = new DateTime();
            $offset = $now->getOffset();
            foreach($obj->data->row as $k => $row) {
                if(is_array($row->v)) {
                    foreach($row->v as $j => $value) {
                        $value = ($value == 'NaN') ? null:$value;
                        $time = ($timeOffset) ? ($row->t+$offset) : ($row->t);
                        $values[$direction[$j]][$time*$multiplier] = $value;
                    }
                    $results->values = $values;
                } else {
                    $value = ($row->v == 'NaN') ? null:$row->v;
                    $time = ($timeOffset) ? ($row->t+$offset) : ($row->t);
                    $values[$direction[0]][$time*$multiplier] = $value;
                    $results->values = $values;
                }
            }

            $resultTypes->$type = $results;
        //}
    }

	protected function singleFlot($source,$destination,$metric,$start=false,$end=false,$multiplier=1,$timeOffset=false) {
		$start = ($start)?$start:date("U", time() - 3600);
		$end = ($end)?$end:date("U");
		$pair = Pair::instance($source,$destination);

		$types = array('Avg','Min','Max');

		$resultTypes = new stdClass();

		foreach($types as $type) {
			$json = $pair->getRrdInstance()->json($metric,$start,$end,$type);
			$obj = Zend_Json::decode($json,Zend_Json::TYPE_OBJECT)->xport;

			$results = new stdClass();

			$results->values = new stdClass();

			$zero = 0;

			if(is_array($obj->meta->legend->entry)) {
				$results->labels = $obj->meta->legend->entry;
				foreach($obj->meta->legend->entry as $x => $z) {
					$rrdlabel = explode(" ",$z);
					$direction[$x] = $rrdlabel[1];
					$values[$direction[$x]] = null;
				}
				$results->values = $values;
			} else {
				$results->labels = array($obj->meta->legend->entry);
				$rrdlabel = explode(" ",$obj->meta->legend->entry);
				$direction[0] = $rrdlabel[1];
				$values[$direction[0]] = null;
				$results->values = $values;
			}

			$now = new DateTime();
			$offset = $now->getOffset();
			foreach($obj->data->row as $k => $row) {
				if(is_array($row->v)) {
					foreach($row->v as $j => $value) {
						$value = ($value == 'NaN') ? null:$value;
						$time = ($timeOffset) ? ($row->t+$offset) : ($row->t);
						$values[$direction[$j]][$time*$multiplier] = $value;
					}
					$results->values = $values;
				} else {
					$value = ($row->v == 'NaN') ? null:$row->v;
					$time = ($timeOffset) ? ($row->t+$offset) : ($row->t);
					$values[$direction[0]][$time*$multiplier] = $value;
					$results->values = $values;
				}
			}

			$resultTypes->$type = $results;
		}

		//$json = $pair->getRrdInstance()->json($metric,$start,$end);

		return $resultTypes;
	}

	protected function multiFlot($source,$destination) {
		$pair = Pair::instance($source,$destination);
		$metrics = $pair->getMetrics();

		$results = array();

		foreach($metrics as $k => $metric) {
			$results[$metric->name] = $this->singleFlot($source,$destination,$metric->name);
		}

		return $results;
	}

	public function action_flot($source,$destination=false,$metric=false) {
		$this->auto_render = false;

		if($metric) {
			$results[$metric] = $this->singleFlot($source,$destination,$metric);
		} else {
			$results = $this->multiFlot($source,$destination);
		}

		$this->response->headers('Content-Type','application/json');
		$this->response->body(Zend_Json::encode($results));
	}

	public function action_lastResultsFromPair($source,$destination) {
		$this->auto_render = false;

		$pair = Pair::instance($source,$destination);

		if(true || Request::current()->is_ajax()) $this->response->headers('Content-Type','application/json');
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
