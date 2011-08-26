<?php
/**
 * Controller Skeleton: Controller de Template que
 */

class Controller_Skeleton extends Controller_Template {

	protected $session;
	public $template = 'templates/skeleton';

	// Controls access for the whole controller, if not set to FALSE we will only allow user roles specified
	// Can be set to a string or an array, for example 'login' or array('login', 'admin')
	// Note that in second(array) example, user must have both 'login' AND 'admin' roles set in database
	public $auth_required = FALSE;

	// Controls access for separate actions
	// 'adminpanel' => 'admin' will only allow users with the role admin to access action_adminpanel
	// 'moderatorpanel' => array('login', 'moderator') will only allow users with the roles login and moderator to access action_moderatorpanel
	public $secure_actions = FALSE;

	/**
	 * The before() method is called before your controller action.
	 * In our template controller we override this method so that we can
	 * set up default values. These variables are then available to our
	 * controllers if they need to be modified.
	 */
	public function before() {
		parent::before();

		#Open session
		$this->session = Session::instance();

		#Check user auth and role
		$action_name = Request::current()->action();
		if (($this->auth_required !== FALSE && Auth::instance()->logged_in($this->auth_required) === FALSE)
				|| (is_array($this->secure_actions) && array_key_exists($action_name, $this->secure_actions) &&
						Auth::instance()->logged_in($this->secure_actions[$action_name]) === FALSE)) {
                        
			if (Auth::instance()->logged_in()) {
				Request::current()->redirect('account/noaccess');
			} else {
				Request::current()->redirect('account/signin');
			}
		}

		if ($this->auto_render) {
			// Initialize empty values
			$this->template->title = 'NetmetricMoM :: ';
			$this->template->content = '';
			$this->template->header = View::factory('templates/mainmenu');
			$this->template->header->menus = Kohana::config('menus.main');
			if (Auth::instance()->logged_in()) {
				$this->template->header->menus['logoff'] = array('title'=>__('Logout'),'href'=>'account/signout');
			}
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
				'css/cupertino/jquery-ui-1.8.15.custom.css' => 'all',
			);

			$jqueryVersion = "1.6.2";
			$juiversion = "1.8.15.custom";


			$scripts = array(
				"js/dev/jquery-$jqueryVersion.js",
				'js/jquery.tools.min.js',
				'js/dev/dummyConsole.js',
				"js/jquery-ui-$juiversion.min.js",
				'js/jquery.tablesorter.min.js',
				'js/dev/jquery.inputmask.js',
				//'js/firebug-lite.js',
			);

			$this->template->styles = array_merge($styles,$this->template->styles);
			$this->template->scripts = array_merge($scripts,$this->template->scripts);
			$this->template->footer->breadcumb = $this->template->title;
		}
		parent::after();
	}

	public function setTemplate($temp) {
		$this->template = $temp;
	}
}
