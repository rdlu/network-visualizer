<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marcelo
 * Date: 3/31/11
 * Time: 4:18 PM
 * To change this template use File | Settings | File Templates.
 */

class Controller_Winagent extends Controller_Skeleton {

    public $auth_required = array('login', 'config');
    public $secure_actions = FALSE;

    public function before() {
        parent::before();
        $this->template->title .= 'Medições do Agente Windows';
    }

    public function after() {
		if ($this->auto_render) {
			$styles = array(
				'css/winagent.css' => 'all',
			);

			$scripts = array(
                                'js/dev/validate.js',
				'js/dev/winagent.js'
			);

			$this->template->styles = array_merge($styles,$this->template->styles);
			$this->template->scripts = array_merge($scripts,$this->template->scripts);
		}
		parent::after();
    }

    public function action_index(){
        $results_per_page = 20;
        $page = (int) $this->request->query('page', null);
        $filter = $this->request->query('filter', null);
        $q = $this->request->query('q', null);
        if($q === null && $filter === null){
            $total_medicoes = count(ORM::factory('dyndata')->find_all());
            $page = $this->set_page($page, $total_medicoes, $results_per_page);
            $start_at = $this->start_at($page, $total_medicoes, $results_per_page);
            $medicoes = ORM::factory('dyndata')->order_by('timestamp', 'DESC')->limit($results_per_page)->offset($start_at)->find_all();
        }
        else {
            switch($filter){
                case 'username': {
                    $username = $this->request->query('q', null);
                    $total_medicoes = count(ORM::factory('dyndata')->where('username', 'like', $username.'%')->find_all());
                    $page = $this->set_page($page, $total_medicoes, $results_per_page);
                    $start_at = $this->start_at($page, $total_medicoes, $results_per_page);
                    $medicoes = ORM::factory('dyndata')->where('username', 'like', $username.'%')->order_by('timestamp', 'DESC')->limit($results_per_page)->offset($start_at)->find_all();
                    //var_dump($start_at); die();
                    break;
                }
                case 'cellid': {
                    $cellid = $this->request->query('q', null);
                    $total_medicoes = count(ORM::factory('dyndata')->where('cellid', '=', $cellid)->find_all());
                    $page = $this->set_page($page, $total_medicoes, $results_per_page);
                    $start_at = $this->start_at($page, $total_medicoes, $results_per_page);
                    $medicoes = ORM::factory('dyndata')->where('cellid', '=', $cellid)->order_by('timestamp', 'DESC')->limit($results_per_page)->offset($start_at)->find_all();

                    break;
                }
                case 'timestamp': {

                    break;
                }
            }
        }

        //if($page === null || $page < 1){
        //    $page = 1;
        //}
        //if($page > ceil($total_medicoes/$results_per_page)){
        //    $page = ceil($total_medicoes/$results_per_page);
        //}
        //$start_at = ($page -1) * $results_per_page;
       
        $view = View::factory('winagent/index')
                                ->bind('medicoes', $medicoes)
                                ->bind('page', $page)
                                ->bind('results_per_page', $results_per_page)
                                ->bind('total_medicoes', $total_medicoes)
                                ->bind('filter', $filter)
                                ->bind('q', $q);
        $this->template->content = $view;
    }
    public function action_filters(){
        $type = $this->request->query('type', null);
        if($type !== null){
            switch($type){
                case 'username': {
                    $username = $this->request->query('username', null);
                    $data = ORM::factory('dyndata')->where('username', 'like', $username.'%');
                    if($this->request->is_ajax()){
                        $this->response->headers('Content-Type', 'text/json');
                        $this->response->body(json_encode($data));
                    }
                    else {
                        $results_per_page = 20;
                    $page = (int) $this->request->query('page', null);
                    $total_medicoes = count(ORM::factory('dyndata')->where('username', 'like', $username.'%')->find_all());
                    if($page === null || $page < 1){
                        $page = 1;
                    }
                    if($page > ceil($total_medicoes/$results_per_page)){
                        $page = ceil($total_medicoes/$results_per_page);
                    }
                    $start_at = ($page -1) * $results_per_page;
                    $medicoes = ORM::factory('dyndata')->where('username', 'like', $username.'%')->order_by('timestamp', 'DESC')->limit($results_per_page)->offset($start_at)->find_all();

                    
                    $view = View::factory('winagent/index')
                                            ->bind('medicoes', $medicoes)
                                            ->bind('page', $page)
                                            ->bind('results_per_page', $results_per_page)
                                            ->bind('total_medicoes', $total_medicoes)
                                            ->bind('filter', $type)
                                            ->bind('username', $username);
                    $this->template->content = $view;
                    }
                    break;
                }
                case 'cellid': {
                    break;
                }
                case 'data' : {
                    break;
                }
            }
        }
    }
    public function set_page($page, $total_medicoes, $results_per_page){
        if($page === null || $page < 1){
            $page = 1;
        }
        if($page > ceil($total_medicoes/$results_per_page)){
            $page = ceil($total_medicoes/$results_per_page);
        }
        return $page;
    }

    public function start_at($page, $total_medicoes, $results_per_page){
        $start_at = ($page -1) * $results_per_page;
        return($start_at);
    }
} //ceil($total_medicoes/$results_per_page);