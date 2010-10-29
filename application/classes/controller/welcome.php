<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller_Skeleton {

    public function before() {
        parent::before();
        $this->template->title .= 'Início :: ';
    }

	public function action_index()
	{
        $view = View::factory('index/welcome');
		$this->template->content = $view;
	}

} // End Welcome
