<?php defined('SYSPATH') or die('No direct script access.');
/* controller para a aba Sintetização */
class Controller_Synthesizing extends Controller_Skeleton {

    public function before() {
        parent::before();
        $this->template->title .= "Sintetização";
    }

    public function action_index() {
        $view = View::factory('synthesizing/index');
        //$entities = Sprig::factory('entity')->load(null, FALSE);
        $processes = Sprig::factory('process')->load(Db::select()->group_by('source_id'),null);
	$resp = array();
	foreach($processes as $process) {
            $resp[] = $process->source->load()->as_array();
	}
        //Fire::info($resp, 'Array com os dados de origem: ');
        $view->bind('resp', $resp);
        $this->template->content = $view;
    }

    public function after(){
        
        if ($this->auto_render) {          

            $scripts = array(                               
                'js/dev/synthesizing.js'
            );            
            $this->template->scripts = array_merge($scripts,$this->template->scripts);
        }
        parent::after();
    }
/*
 * Recebe o id de uma sonda e
 * Retorna as informações JSON sobre as sondas de destino
 */
    public function action_destsondas($source){
        // Antes de Rodrigo
/*
        if (Request::current()->is_ajax()) {
            $this->auto_render = false;

            $sonda1 = array(
                        "results" => array(
                                "throughput" => "67388974.018",
                                "throughputTCP" => "67388974.018",
                                "loss" => "0",
                                "rtt" => "-0.022973",
                                "jitter" => "0.000405",
                                "owd" => "-1479.070217",
                                "pom" => "0",
                                "capacity" => "0",
                                "mos" => "4.5"
                        ),
                        "thresholds" => array(
                                "throughput" => array(
                                    "thresholdprofile_id" => "1",
                                    "metric_id" => "1",
                                    "min" => "2457600",
                                    "max" => "6553600",
                                    "id" => "1"
                                ),
                                "throughputTCP" => array(
                                    "thresholdprofile_id" => "1",
                                    "metric_id" => "1",
                                    "min" => "2457600",
                                    "max" => "6553600",
                                    "id" => "1"
                                ),
                                "jitter" => array(
                                        "thresholdprofile_id" => "1",
                                        "metric_id" => "2",
                                        "min" => "0.04",
                                        "max" => "0.1",
                                        "id" => "3"
                                ),
                                "capacity" => array(
                                        "thresholdprofile_id" => "1",
                                        "metric_id" => "3",
                                        "min" => "2457600",
                                        "max" => "6553600",
                                        "id" => "4"
                                ),
                                "loss" => array(
                                        "thresholdprofile_id" => "1",
                                        "metric_id" => "4",
                                        "min" => "5",
                                        "max" => "20",
                                        "id" => "5"
                                ),
                                "owd" => array(
                                        "thresholdprofile_id" => "1",
                                        "metric_id" => "5",
                                        "min" => "0.075",
                                        "max" => "0.125",
                                        "id" => "6"
                                ),
                                "mos" => array(
                                        "thresholdprofile_id" => "1",
                                        "metric_id" => "6",
                                        "min" => "1",
                                        "max" => "4",
                                        "id" => "7"
                                ),
                                "pom" => array(
                                        "thresholdprofile_id" => "1",
                                        "metric_id" => "7",
                                        "min" => "10",
                                        "max" => "5",
                                        "id" => "8"
                                ),
                                "rtt" => array(
                                        "thresholdprofile_id" => "1",
                                        "metric_id" => "8",
                                        "min" => "0.25",
                                        "max" => "0.15",
                                        "id" => "9"
                                )
                        ),
                        "target" => array(
                                "id" => 7,
                                "ip" => "143.54.10.79",
                                "name" => "mt-cuiab\u00e1"
                        )
	         
        );


            $JSONresponse[] = $sonda1;

            $this->response->headers('Content-Type', 'application/json');
            $this->response->body(Zend_Json::encode($JSONresponse));
 } */
            //após Rodrigo
           
/* ESSE É O QUE DEVE ESTAR FUNCIONANDO */

        $this->auto_render = false;		

		$source = Sprig::factory('entity',array('id'=>$source))->load();
		$processes = Sprig::factory('process')->load(Db::select()->group_by('destination_id')->where('source_id','=',$source->id),null);
		$resp = array();
		foreach($processes as $process) {
			$resp[] = $process->destination->load();
		}

		foreach($resp as $destination) {
			$pair = Pair::instance($source->id,$destination->id);
			$resultss[$destination->id] = $pair->lastResults();
		}
                if(Request::current()->is_ajax()) $this->response->headers('Content-Type','application/json');
                $this->response->body(Zend_Json::encode($resultss));
                   

 }
//recebe o id da sonda de origem e retorna informações sobre a sonda de origem
     public function action_origsondas($idorigem){
        if (Request::current()->is_ajax()) {
            $this->auto_render = false;
            $sonda_origem = Sprig::factory('entity', array("id" => $idorigem))->load();
            //Sprig::factory('entity', array("id" => $id))->load(
            $JSONresponse = array(
                "id" => $sonda_origem->id,
                "ipaddress" =>  $sonda_origem->ipaddress,
                "name" =>  $sonda_origem->name,
                "status" =>  $sonda_origem->status
             );
            $this->response->headers('Content-Type', 'text/json');
            $this->response->body(JSON_encode($JSONresponse));
       }
    }

    public function action_popup($sondaOrigemId){
        $view = View::factory('synthesizing/popup');
	$view->bind('sondaOrigemId', $sondaOrigemId);
        $this->template->content = $view;
    }
}