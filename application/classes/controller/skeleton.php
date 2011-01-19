<?php
/**
 * Controller Skeleton: Controller de Template que
 */
 
class Controller_Skeleton extends Controller_Template {

  	public $template = 'templates/skeleton';

    /**
     * The before() method is called before your controller action.
     * In our template controller we override this method so that we can
     * set up default values. These variables are then available to our
     * controllers if they need to be modified.
     */
    public function before() {
        parent::before();

  	    if ($this->auto_render) {
  	    	// Initialize empty values
  	    	$this->template->title   = 'NetmetricMoM :: ';
  	    	$this->template->content = '';
            $this->template->header = View::factory('templates/mainmenu');
            $footer = View::factory('templates/footer');
            $this->template->footer = $footer;

  		$this->template->styles = array();
  		$this->template->scripts = array();

        }
    }

    /**
     * The after() method is called after your controller action.
     * In our template controller we override this method so that we can
     * make any last minute modifications to the template before anything
     * is rendered.
     */
    public function after() {
        if ($this->auto_render) {
            $styles = array(
                'css/reset.css' => 'all',
                'css/common.css' => 'all',
                'css/screen.css' => 'screen, projection',
                'css/print.css' => 'print',
                'css/mobile.css' => 'mobile',
                'css/tablesorter/blue.css' => 'screen, projection',
                'css/cupertino/jquery-ui-1.8.6.custom.css' => 'all',
            );

            $scripts = array(
                'js/dev/jquery-1.4.4.js',
                'js/jquery-ui-1.8.6.custom.min.js',
                'js/jquery.tablesorter.min.js',
	             'js/dev/jquery.inputmask.js',
                //'js/firebug-lite.js',
            );

            $this->template->styles = array_merge( $this->template->styles, $styles );
            $this->template->scripts = array_merge( $this->template->scripts, $scripts );
            $this->template->footer->breadcumb = $this->template->title;
        }
        parent::after();
    }

    public function setTemplate($temp) {
        $this->template = $temp;
    }
  }
