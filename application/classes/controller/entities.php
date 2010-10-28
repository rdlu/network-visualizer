<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Entities extends Controller_Skeleton {

    public function before() {
        parent::before();
        $this->template->title .= 'Entidades :: ';
    }

	public function action_index()
	{
		$entities = Sprig::factory('entity')->load(NULL, FALSE);
        Fire::group('Models Loaded')->info($entities)->groupEnd();
        $view = View::factory('entities/list');

        $view->bind('entities',$entities);

        $this->template->content = $view;
	}

    public function action_edit($id) {
        $entity = Sprig::factory('entity');

        if($id!=0) {
            $entity->id = $id;
            $entity->load();
        }

        if ($_POST) {
            try {
                $entity->values($_POST)->create();
                $this->request->redirect(Route::get('entities')->uri(array('name' => $project->name)));
            } catch (Validate_Exception $e) {
                $errors = $e->array->errors('entities/new');
                Fire::group('Form Validation Results')->warn($errors)->groupEnd();
            }
        }
        $view = View::factory('entities/form');
        $view->bind('entity',$entity)->bind('errors',$errors);
        if($id==0 || $entity->loaded()) $this->template->content = $view;
        else $this->template->content = 'Entidade não existente no MoM';
    }

    public function action_new() {
        $this->action_edit(0);
    }

} // End Welcome
