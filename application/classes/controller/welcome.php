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
            $this->request->response = $view;
        }
    }

} // End Welcome
