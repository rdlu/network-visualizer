<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marcelo
 * Date: 3/31/11
 * Time: 4:18 PM
 * To change this template use File | Settings | File Templates.
 */

class Controller_Winagent extends Controller_Skeleton
{

    public $auth_required = FALSE;
    public $secure_actions = 'login';

    public function before()
    {
        parent::before();
        $this->template->title .= 'Medições do Agente Windows';
    }

    public function after()
    {
        if ($this->auto_render) {
            $styles = array(
                'css/winagent.css' => 'all',
            );

            $scripts = array(
                'js/dev/validate.js',
                'js/dev/winagent.js'
            );

            $this->template->styles = array_merge($styles, $this->template->styles);
            $this->template->scripts = array_merge($scripts, $this->template->scripts);
        }
        parent::after();
    }

    public function action_index()
    {
        $results_per_page = 20;
        $page = (int)$this->request->query('page', null);
        $filter = $this->request->query('filter', null);
        $q = $this->request->query('q', null);
        $inicio = null;
        $fim = null;
        if ($q === null && $filter === null) {
            $pagination = Pagination::factory(array(
                'total_items' => ORM::factory('dyndata')->count_all(),
                'items_per_page' => $results_per_page,
                'view' => 'pagination/floating',
                'auto_hide' => FALSE,
            ));

            $medicoes = ORM::factory('dyndata')->order_by('timestamp', 'DESC')->limit($pagination->items_per_page)
                ->offset($pagination->offset)->find_all();
            ;
        } else {
            switch ($filter) {
                case 'username':
                    {
                    $username = $this->request->query('q', null);

                    $pagination = Pagination::factory(array(
                        'total_items' => ORM::factory('dyndata')->where('username', 'like', $username . '%')->count_all(),
                        'items_per_page' => $results_per_page,
                        'view' => 'pagination/floating',
                        'auto_hide' => FALSE,
                    ));

                    $medicoes = ORM::factory('dyndata')
                        ->where('username', 'like', $username . '%')
                        ->order_by('timestamp', 'DESC')
                        ->limit($pagination->items_per_page)
                        ->offset($pagination->offset)->find_all();
                    //var_dump($start_at); die();
                    break;
                    }
                case 'cellid':
                    {
                    $cellid = $this->request->query('q', null);
                    $pagination = Pagination::factory(array(
                        'total_items' => ORM::factory('dyndata')->where('cellid', '=', $cellid)->count_all(),
                        'items_per_page' => $results_per_page,
                        'view' => 'pagination/floating',
                        'auto_hide' => FALSE,
                    ));

                    $medicoes = ORM::factory('dyndata')->where('cellid', '=', $cellid)->order_by('timestamp', 'DESC')->limit($pagination->items_per_page)
                        ->offset($pagination->offset)->find_all();
                    break;
                    }
                case 'timestamp':
                    {
                    $inicio = $this->request->query('inicio', null);
                    $fim = $this->request->query('fim', null);
                    $q = 'inicio=' . $inicio . '&fim=' . $fim;


                    $inicio = strtotime(substr($inicio, 3, 2) . '/' . substr($inicio, 0, 2) . '/' . substr($inicio, -4));
                    $fim = strtotime(substr($fim, 3, 2) . '/' . substr($fim, 0, 2) . '/' . substr($fim, -4));

                    if ($inicio > $fim) {
                        $tmp = $inicio;
                        $inicio = $fim;
                        $fim = $tmp;
                    }

                    $pagination = Pagination::factory(array(
                        'total_items' => ORM::factory('dyndata')->where('timestamp', '>=', $inicio)->where('timestamp', '<=', $fim)->count_all(),
                        'items_per_page' => $results_per_page,
                        'view' => 'pagination/floating',
                        'auto_hide' => FALSE,
                    ));

                    $medicoes = ORM::factory('dyndata')->where('timestamp', '>=', $inicio)
                        ->where('timestamp', '<=', $fim)->order_by('timestamp', 'DESC')
                        ->limit($pagination->items_per_page)
                        ->offset($pagination->offset)->find_all();
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
            ->bind('filter', $filter) //opcional: filtro para os dados
            ->bind('q', $q); //opcional: query para os filtros
        $this->template->pagination = $pagination->render();
        $this->template->content = $view;
    }


    public function action_filters()
    {
        $type = $this->request->query('type', null);
        if ($type !== null) {
            switch ($type) {
                case 'username':
                    {
                    $username = $this->request->query('username', null);
                    $data = ORM::factory('dyndata')->where('username', 'like', $username . '%');
                    if ($this->request->is_ajax()) {
                        $this->response->headers('Content-Type', 'text/json');
                        $this->response->body(json_encode($data));
                    } else {
                        $results_per_page = 20;
                        $page = (int)$this->request->query('page', null);
                        $total_medicoes = count(ORM::factory('dyndata')->where('username', 'like', $username . '%')->find_all());
                        if ($page === null || $page < 1) {
                            $page = 1;
                        }
                        if ($page > ceil($total_medicoes / $results_per_page)) {
                            $page = ceil($total_medicoes / $results_per_page);
                        }
                        $start_at = ($page - 1) * $results_per_page;
                        $medicoes = ORM::factory('dyndata')->where('username', 'like', $username . '%')->order_by('timestamp', 'DESC')->limit($results_per_page)->offset($start_at)->find_all();


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
                case 'cellid':
                    {
                    break;
                    }
                case 'data' :
                    {
                    break;
                    }
            }
        }
    }

    protected function set_page($page, $total_medicoes, $results_per_page)
    {
        if ($page === null || $page < 1) {
            $page = 1;
        }
        if ($page > ceil($total_medicoes / $results_per_page)) {
            $page = ceil($total_medicoes / $results_per_page);
        }
        return $page;
    }

    protected function start_at($page, $total_medicoes, $results_per_page)
    {
        $start_at = ($page - 1) * $results_per_page;
        return ($start_at);
    }
} //ceil($total_medicoes/$results_per_page);