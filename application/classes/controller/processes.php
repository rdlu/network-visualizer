<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Processes extends Controller {


	public function action_index() {
		$processes = Sprig::factory('process')->load(NULL, FALSE);
        Fire::group('Models Loaded')->info($processes)->groupEnd();
        /*$view = View::factory('processes/list');
        $view->bind('processes',$processes);
        $this->template->content = $view;*/
	}

    public function action_new($source = 0,$destination = 0) {
        $process = Sprig::factory('process');

        if($source!=0) $process->source = $source;

        if($destination!=0) $process->destination = $destination;

        if ($_POST) {
            try {
                $process->values($_POST)->create();
            } catch (Validate_Exception $e) {
                $errors = $e->array->errors('processes/edit');
                Fire::group('Form Validation Results')->warn($errors)->groupEnd();
            }
        }
        $view = View::factory('processes/form');
        $view->bind('process',$process)->bind('errors',$errors);
        $this->request->response = $view;
    }
    
    public function action_remove($id) {
        $process = Sprig::factory('process');
        if($id!=0) {
            $process->id = $id;
            $process->load();
        }

        if($process->loaded()) {
            $view = View::factory('process/remove');
            $view->set('name',$process->name);
            $process->delete();
            $this->template->content = $view;
        } else $this->template->content = 'Processo não existente no MoM';
    }

    public function action_view($id) {
        $id = (int) $id;
        $view = View::factory('process/view');

        $entity = Sprig::factory('entity',array('id'=>$id))->load();

        if($entity->loaded()) {
            $view->bind('entity',$entity);
            $this->template->content = $view;
        } else {
            $this->template->content = 'Entidade não localizada no sistema';
        }
    }
} // End Welcome
