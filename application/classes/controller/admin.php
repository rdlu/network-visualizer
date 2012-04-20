<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin extends Controller_Skeleton {

	public $auth_required = 'login';

	// Controls access for separate actions
	// 'adminpanel' => 'admin' will only allow users with the role admin to access action_adminpanel
	// 'moderatorpanel' => array('login', 'moderator') will only allow users with the roles login and moderator to access action_moderatorpanel
	public $secure_actions = FALSE;

    public function before() {
        parent::before();
        $this->template->title .= 'Painel de Controle :: ';
    }

	public function action_index() {
        $view = View::factory('admin/index');
        $this->template->content = $view;
	}

} // End Welcome
