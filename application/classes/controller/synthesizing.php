<?php defined('SYSPATH') or die('No direct script access.');
/* controller para a aba Sintetização */
class Controller_Synthesizing extends Controller_Skeleton {

    public function before() {
        parent::before();
        $this->template->title .= "Sintetização";

    }

    public function action_index() {
        $view = View::factory('synthesizing/index');
        $entities = Sprig::factory('entity')->load(null, FALSE);
        $view->bind('entities', $entities);
        $this->template->content = $view;
    }

    public function after(){
        parent::after();
        if ($this->auto_render) {          

            $scripts = array(               
                'js/dev/jquery.progressbar.js',
                'js/dev/synthesizing.js'
            );

            $this->template->scripts = array_merge( $this->template->scripts, $scripts );
        }
    }

    public function action_povoasondasdestino($idorigem){
        $JSONresponse[0] = array(
           
             "id" => 3,
             "ip" => "143.54.10.199",
             "nome" => "nm1",
             "rtt" =>  0.0012,
             "loss" => "15.77972",
             "tpUDP" => "47.92972",
             "tpTCP" => "12.12233",
             "erros" => array()
         );
         $JSONresponse[0] = array(
             "id" => 4,
             "ip" => "143.54.10.77",
             "nome" => "nm2",
             "rtt" =>  0.0012,
             "loss" => "5.972",
             "tpUDP" => "7.92972",
             "tpTCP" =>  "1.2233",
             "erros" => array()
        );
        $this->response->headers['Content-Type'] = 'text/json';
        $this->response->body = json_encode($JSONresponse);
    }
}

