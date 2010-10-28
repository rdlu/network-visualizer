<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Profiles extends Controller_Skeleton {

    public function before() {
        parent::before();
        $this->template->title .= 'Perfis de Medição :: ';
    }

	public function action_index()
	{
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
                $this->request->redirect(Route::get('profiles')->uri(array('name' => $project->name)));
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

} // End Welcome
