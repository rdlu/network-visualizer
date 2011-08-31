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
				'js/dev/winagent.js'
			);

			//$this->template->styles = array_merge($styles,$this->template->styles);
			//$this->template->scripts = array_merge($scripts,$this->template->scripts);
		}
		parent::after();
    }

    public function action_index(){
        $results_per_page = 20;
        $page = $this->request->query('page', null);
        if($page === null || $page <= 0) $page = 1;
        $start_at = ($page -1) * $results_per_page;
        $medicoes = ORM::factory('dyndata')->limit($results_per_page)->offset($start_at)->find_all();
        $view = View::factory('winagent/index')
                    ->bind('medicoes', $medicoes)
                    ->bind('page', $page)
                    ->bind('results_per_page', $results_per_page);
        $this->template->content = $view;
    }
}