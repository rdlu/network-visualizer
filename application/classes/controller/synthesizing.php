<?php defined('SYSPATH') or die('No direct script access.');
/* controller para a aba Sintetização */
class Controller_Synthesizing extends Controller_Skeleton {

    public function before() {
        parent::before();
        $this->template->title .= "Sintetização";

    }

    public function action_index() {
        $view = View::factory('synthesizing/index');
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
}
