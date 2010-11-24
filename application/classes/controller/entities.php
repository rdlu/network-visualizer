<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Entities extends Controller_Skeleton {

    public function before() {
        parent::before();
        $this->template->title .= 'Entidades :: ';
    }

	public function action_index() {
        $this->template->title .= 'Listagem';
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
            $this->template->title .= "Editando a entidade $entity->ipaddress";
        }

        if ($_POST) {
            try {
                $entity->values($_POST)->create();
                $this->request->redirect($this->request->controller.'/edit/'.$entity->id);
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

    public function action_remove($id) {
        $entity = Sprig::factory('entity');
        if($id!=0) {
            $entity->id = $id;
            $entity->load();
        }

        if($entity->loaded()) {
            $view = View::factory('entities/remove');
            $view->set('name',$entity->name);
            $entity->delete();
            $this->template->content = $view;
        } else $this->template->content = 'Entidade não existente no MoM';
    }

    public function action_view($id) {
        $id = (int) $id;
        $view = View::factory('entities/view');

        $entity = Sprig::factory('entity',array('id'=>$id))->load();

        if($entity->loaded()) {
            $view->bind('entity',$entity);
            $this->template->content = $view;
        } else {
            $this->template->content = 'Entidade não localizada no sistema';
        }
    }
} // End Welcome
