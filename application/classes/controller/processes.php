<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Processes extends Controller_Skeleton {

    public function before() {
        parent::before();
        $this->template->title .= 'Processos de Medição :: ';
    }

	public function action_index($sourceAddr=0) {
        $view = View::factory('processes/list');

        if(!$sourceAddr) $sourceAddr = '127.0.0.1';

        if(Validate::ip($sourceAddr)){
            $sourceEntity = Sprig::factory('entity',array('ipaddress'=>$sourceAddr))->load();
            $entities = Sprig::factory('entity')->load(NULL, FALSE);

            if($sourceEntity->id) {
                $processes = Sprig::factory('process',array('source_id',$sourceEntity->id))->load(NULL, FALSE);
            } else {
                $processes = null;
            }

            Fire::group('Models Loaded')
                    ->info($processes)
                    ->info($sourceEntity)
                    ->info($entities)
                    ->groupEnd();
        } else {
            $errors[] = "A origem $sourceAddr não é um IP válido.";
        }


        $view->bind('processes',$processes)
                ->bind('errors',$errors)
                ->bind('source',$sourceEntity)
                ->bind('sourceAddr',$sourceAddr)
                ->bind('entities',$entities);
        $this->template->content = $view;
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
        $this->template->content = $view;
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
