<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller_Skeleton {

    public function before() {
        parent::before();
        $this->template->title .= 'Início :: ';
    }

	public function action_index() {
		$view = View::factory('index/welcome');
		$this->template->content = $view;
		$this->template->extras = View::factory('index/tJS');
	}

    public function after() {
        parent::after();
        if ($this->auto_render) {
            $styles = array(
                'css/map.css' => 'all',
            );

            $scripts = array(
                'http://maps.google.com/maps/api/js?sensor=false',
                'js/dev/interface.js'
            );

            $this->template->styles = array_merge( $this->template->styles, $styles );
            $this->template->scripts = array_merge( $this->template->scripts, $scripts );
        }
    }

    public function action_infoMapa() {
        if(Request::$is_ajax) {
            $this->auto_render = false;
            $entidades = Sprig::factory('entity')->load(null,FALSE);

            $view = View::factory('xml/infoMapa');

            $view->bind('entities', $entidades);

            //$this->request->headers['Content-Type'] = 'application/xml';
            $this->request->headers['Content-Type'] = 'text/xml';
            $this->request->response = $view;
        }
    }

    public function action_infoMapaJ(){
        if(Request::$is_ajax){
            $this->auto_render = false;
            $query = DB::select()->order_by('status', 'DESC');
            $entities = Sprig::factory('entity')->load($query, FALSE);
            $JSONresponse = array();            
            
            foreach($entities as $k => $entity){ //coloca as medições em um array
                $agentes = array();
                $gerentes = array();
                foreach ($entity->processes_as_source as $process){
                    $agentes[] = $process->destination->id;
                }
                foreach ($entity->processes_as_destination as $process){
                    $gerentes[] = $process->source->id;
                }

                $JSONresponse[$k] = array( //prepara um array com a resposta
                    'id' => $entity->id,
                    'ip' => $entity->ipaddress,
                    'nome' => $entity->name,
                    'status' => $entity->status,
                    'latitude' => $entity->latitude,
                    'longitude' => $entity->longitude,
                    'agentes' => $agentes,
                    'gerentes' => $gerentes
                );
            }
            $this->request->header['Content-Type'] = 'text/json';
            $this->request->response = json_encode($JSONresponse);
        }
    }

    public function action_infoBar($id) {
        if(Request::$is_ajax) {
            $this->auto_render = false;

            $id = (int) $id;

            $dados = Sprig::factory('entity', array("id" => $id))->load();

            $this->request->headers['Content-Type'] = 'text/json';

            $this->request->response = JSON_encode( array(
                'endereco' => "$dados->address, $dados->addressnum",
                'localidade' => "$dados->state, $dados->city",
                'status' => "$dados->status"                
            ));
        }
    }
} // End Welcome
