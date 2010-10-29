<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin extends Controller_Skeleton {

    public function before() {
        parent::before();
        $this->template->title .= 'Painel de Controle :: ';
    }

	public function action_index() {
        $view = View::factory('admin/index');
        $this->template->content = $view;
	}

} // End Welcome
