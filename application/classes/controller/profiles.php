<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Profiles extends Controller_Skeleton {

    public function before() {
        parent::before();
        $this->template->title .= 'Perfis de Teste :: ';
    }

	public function action_index() {
		$profiles = Sprig::factory('profile')->load(NULL, FALSE);
        Fire::group('Models Loaded')->info($profiles)->groupEnd();
        $view = View::factory('profiles/list');

        $view->bind('profiles',$profiles);

        $this->template->content = $view;
	}

    public function action_edit($id) {
        $profile = Sprig::factory('profile');

        if($id!=0) {
            $profile->id = $id;
            $profile->load();
        }

        if ($_POST) {
            try {
                $profile->values($_POST)->create();
                $this->request->redirect('profiles');
            } catch (Validate_Exception $e) {
                $errors = $e->array->errors('profiles/new');
                Fire::group('Form Validation Results')->warn($errors)->groupEnd();
            }
        }
        $view = View::factory('profiles/form');
        $view->bind('profile',$profile)->bind('errors',$errors);
        if($id==0 || $profile->loaded()) $this->template->content = $view;
        else $this->template->content = 'Perfill não existente no MoM';
    }

    public function action_new() {
        $this->action_edit(0);
    }

    public function action_info() {
        $this->auto_render = false;
        if(!isset($_POST['profile'])) throw new Kohana_Exception('Compulsory data not set, must be called with post',$_POST);
        else {
            $post = (int) $_POST['profile'];
        }
        $profile = Sprig::factory('profile',array('id'=>$post))->load();

        $q = array(
            'id' => $profile->id,
            'name' => $profile->name,
            'description' => $profile->description
        );

        foreach($profile->metrics as $metric) {
            $q['metrics'][$metric->id] = $metric->name;
        }
        
        $this->request->headers['Content-Type'] = 'application/json';
        if(Request::$is_ajax) $this->request->response = json_encode($q);
        else throw new Kohana_Exception('This controller only accepts AJAX requests',$_POST);
    }

} // End Welcome
