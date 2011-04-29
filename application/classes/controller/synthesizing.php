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
        if (Request::current()->is_ajax()) {
            $this->auto_render = false;
// Antes de Rodrigo
/*
            $sonda1 = array(
                   'resultados' => array(
                        "id" => 6,
                        "ip" => "143.54.10.199",
                        "nome" => "nm1",
                        "rtt" =>  1,
                        "loss" => "2",
                        "tpUDP" => "3",
                        "tpTCP" => "4"
                    ),
                    'limiares' => array(
                        "rttMin" => 1,
                        "rttMax" => 10,
                        "lossMin" => 1,
                        "lossMax" => 10,
                        "tpTcpMin" => 1,
                        "tpTcpMax" => 10,
                        "tpUdpMin" => 1,
                        "tpUdpMax" => 10
                   )
            );
            $sonda2 = array(
                   'resultados' => array(
                        "id" => 7,
                        "ip" => "143.54.10.200",
                        "nome" => "nm3",
                        "rtt" =>  5,
                        "loss" => "6",
                        "tpUDP" => "7",
                        "tpTCP" => "9"
                   ),
                   'limiares' => array(
                        "rttMin" => 1,
                        "rttMax" => 10,
                        "lossMin" => 1,
                        "lossMax" => 10,
                        "tpTcpMin" => 1,
                        "tpTcpMax" => 10,
                        "tpUdpMin" => 1,
                        "tpUdpMax" => 10
                   )
                );
            $JSONresponse[] = $sonda1;
            $JSONresponse[] = $sonda2;
*/
            /*
            $JSONresponse = array(
                0 => array(
                   'resultados' => array(
                        "id" => 6,
                        "ip" => "143.54.10.199",
                        "nome" => "nm1",
                        "rtt" =>  0.0012,
                        "loss" => "15.77972",
                        "tpUDP" => "47.92972",
                        "tpTCP" => "12.12233"
                   ),
                   'limiares' => array(
                        "rttMin" => 1,
                        "rttMax" => 10,
                        "lossMin" => 1,
                        "lossMax" => 10,
                        "tpTcpMin" => 1,
                        "tpTcpMax" => 10,
                        "tpUdpMin" => 1,
                        "tpUdpMax" => 10
                   )
                ),
                1 => array(
                   'resultados' => array(
                        "id" => 7,
                        "ip" => "143.54.10.200",
                        "nome" => "nm3",
                        "rtt" =>  0.0012,
                        "loss" => "15.77972",
                        "tpUDP" => "47.92972",
                        "tpTCP" => "12.12233"
                   ),
                   'limiares' => array(
                        "rttMin" => 1,
                        "rttMax" => 10,
                        "lossMin" => 1,
                        "lossMax" => 10,
                        "tpTcpMin" => 1,
                        "tpTcpMax" => 10,
                        "tpUdpMin" => 1,
                        "tpUdpMax" => 10
                   )
                )
             );
*/
            /*
            $JSONresponse[0] = array(

                 "id" => 6,
                 "ip" => "143.54.10.199",
                 "nome" => "nm1",
                 "rtt" =>  0.0012,
                 "loss" => "15.77972",
                 "tpUDP" => "47.92972",
                 "tpTCP" => "12.12233"
             );
             $JSONresponse[1] = array(
                 "id" => 7,
                 "ip" => "143.54.10.77",
                 "nome" => "nm2",
                 "rtt" =>  0.0012,
                 "loss" => "5.972",
                 "tpUDP" => "7.92972",
                 "tpTCP" =>  "1.2233"                
            );                  

            $JSONresponse = Reports::lastResultsFromSource($sondaOrigemId);

            $this->response->headers('Content-Type', 'application/json');
            $this->response->body(json_encode($JSONresponse));
             *
             *
             */
            //após Rodrigo
           
		//$source = $_POST['source'];

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