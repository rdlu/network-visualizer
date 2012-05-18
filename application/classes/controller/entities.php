<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Entities extends Controller_Skeleton
{

	public $auth_required = 'login';

	// Controls access for separate actions
	// 'adminpanel' => 'admin' will only allow users with the role admin to access action_adminpanel
	// 'moderatorpanel' => array('login', 'moderator') will only allow users with the roles login and moderator to access action_moderatorpanel
	public $secure_actions = array('remove' => 'config',
        'edit' => 'config',
        'new' => 'config',
        'list' => 'login',
        'view' => 'login',
        'byCity' => 'login',
        'topTenManagers' => 'login',
        'destinations' => 'login',
        'checkRRD' => 'config'
    );

	public function before()
	{
		parent::before();
		$this->template->title .= 'Entidades :: ';
	}

	public function action_index()
	{
		$this->template->title .= 'Listagem';
		$entities = Sprig::factory('entity')->load(NULL, FALSE);
		//Fire::group('Models Loaded')->info($entities)->groupEnd();
		$view = View::factory('entities/list');

		$view->bind('entities', $entities);

		$this->template->content = $view;
	}

	public function action_list()
	{
		$this->auto_render = false;

		$query = Db::select('id', 'city', 'state', 'name', 'ipaddress')->from('entities')->order_by('name', 'ASC');

		if (isset($_POST['city'])) $query = $query->where('city', 'like', $_POST['city'] . '%');
		if (isset($_POST['name'])) $query = $query->where('name', 'like', '%' . $_POST['name'] . '%');
		if (isset($_POST['maxRows'])) $query = $query->limit((int)$_POST['maxRows']);
		if (isset($_POST['excludeId'])) $query = $query->where('id', '!=', $_POST['excludeId']);
		$response['entities'] = $query->execute()->as_array();
		$this->response->headers('Content-Type', 'application/json');
		$this->response->headers('Cache-Control', 'no-cache');
		if (Request::current()->is_ajax()) $this->response->body(json_encode($response));
		else throw new Kohana_Exception('This controller only accepts AJAX requests', $response);
	}

	public function action_destinations($id = 0)
	{
		$this->auto_render = false;

		if (Request::current()->is_ajax()) {
			if ($id == 0) {
				$id = $_POST['id'];
			}

			$source = Sprig::factory('entity', array('id' => $id))->load();
			$processes = Sprig::factory('process')->load(Db::select()->group_by('destination_id')->where('source_id', '=', $source->id), null);
			$resp = array();
			foreach ($processes as $process) {
				$resp[] = $process->destination->load()->as_array();
			}

			$this->response->headers('Content-Type', 'application/json');
			$this->response->headers('Cache-Control', 'no-cache');
			$this->response->body(json_encode($resp));
		} else {
			throw new Kohana_Exception("This controller only accept ajax requests", $_POST);
		}


	}

	public function action_edit($id)
	{
		$entity = Sprig::factory('entity');

		$disabled = 'disabled';
		$sucess = false;

		if ($id != 0) {
			$entity->id = $id;
			$entity->load();
			$this->template->title .= "Editando a entidade $entity->ipaddress";
			$disabled = 'enabled';
		}

		if ($_POST) {
			try {
				$entity->values($_POST)->create();
				Request::current()->redirect(Request::current()->controller() . '/view/' . $entity->id);
				$sucess = true;
			} catch (Validation_Exception $e) {
				$errors = $e->array->errors('entities/new');
				//Fire::group('Form Validation Results')->warn($errors)->groupEnd();
				if (!isset($errors['ipaddress'])) {
					$disabled = 'enabled';
				}
			}
		}

		$view = View::factory('entities/form');
		$view->bind('entity', $entity)
				->bind('errors', $errors)
				->bind('disabled', $disabled)
				->bind('success', $success);
		if ($id == 0 || $entity->loaded()) $this->template->content = $view;
		else $this->template->content = 'Entidade não existente no MoM';
	}

	public function action_new()
	{
		$this->action_edit(0);
	}

	public function action_remove()
	{
        $this->auto_render = false;
        $id = (int) $_POST['id'];
		$entity = Sprig::factory('entity');
		if ($id != 0) {
			$entity->id = $id;
			$entity->load();
		}

		if ($entity->loaded()) {

			if(($entity->processes_as_source->count() == 0) || ($entity->processes_as_destination->count() == 0)) {
				$name = $entity->name;
				$entity->delete();
                $this->response->body("<div id=\"error\" class=\"success\">A entidade".$name."foi removida com sucesso.</div>");
			} else {
                $this->response->body = "Não foi possível remover a sonda $entity->name, ainda existem processos de medição agendados.";
			}

		} else $this->response->body = 'Entidade não existente no MoM';
	}

	public function action_view($id)
	{
		$id = (int) $id;
		$view = View::factory('entities/view');

		$entity = Sprig::factory('entity', array('id' => $id))->load();
		$status = Sonda::instance($entity->id, true);
		$this->template->title .= "Informações da sonda " . $entity->name;

		if ($entity->loaded()) {
			$asSource = Sprig::factory('process')->load(Db::select()->group_by('destination_id')->where('source_id', '=', $entity->id), null);
			$assou = array();
			$assouprocs = array();
			foreach ($asSource as $process1) {
				$ass1 = $process1->destination->load()->as_array();
				$assou['i'.$ass1['id']] = $ass1;
				Pair::instance($entity->id,$ass1['id'])->getProcesses();
				$assouprocs['p'.$ass1['id']] = Pair::instance($entity->id,$ass1['id'])->getProcesses(true);
			}

			$asDestination = Sprig::factory('process')->load(Db::select()->group_by('source_id')->where('destination_id', '=', $entity->id), null);
			$asdest = array();
			$asdestprocs = array();
			foreach($asDestination as $process2) {
				$asd1 = $process2->source->load()->as_array();
				$asdest['i'.$asd1['id']] = $asd1;
				$asdestprocs['p'.$asd1['id']] = Pair::instance($asd1['id'],$entity->id)->getProcesses(true);
			}

			$processes = Sprig::factory('process')->load(Db::select()->or_where('source_id', '=', $entity->id)->or_where('destination_id','=',$entity->id), null);

			$proca = array();
			foreach($processes as $process) {
				$proca[] = $process->as_array();
			}

			$processJSON =Zend_Json::encode($proca);

			$view->bind('entity', $entity)
					->bind('status', $status)
					->bind('destinations', $assou)
					->bind('destinationsProcesses',$assouprocs)
					->bind('sources',$asdest)
					->bind('sourcesProcesses',$asdestprocs)
					->bind('processes',$processes)
					->bind('procJSON',$processJSON);
			$this->template->content = $view;
		} else {
			$this->template->content = 'Entidade não localizada no sistema';
		}
	}

    private function destinations(Model_Entity $entity) {
        $asSource = Sprig::factory('process')->load(Db::select()->group_by('destination_id')->where('source_id', '=', $entity->id), null);
        $assou = array();
        foreach ($asSource as $process1) {
            $ass1 = $process1->destination->load();
            $assou[$ass1['id']] = $ass1;
        }
        return $assou;
    }

    private function sources(Model_Entity $entity) {
        $asDestination = Sprig::factory('process')->load(Db::select()->group_by('source_id')->where('destination_id', '=', $entity->id), null);
        $asdest = array();
        foreach($asDestination as $process2) {
            $asd1 = $process2->source->load();
            $asdest[$asd1->id] = $asd1;
        }

        return $asdest;
    }

	public function action_byCity() {
		$this->auto_render = false;
		if (!isset($_POST['city'])) throw new Kohana_Exception('Compulsory data not set, must be called with post', $_POST);
		$post = 'Port';
		if (isset($_POST['city'])) $post = (string)$_POST['city'];
		$query['entities'] = Db::select('id', 'city', 'state', 'name', 'ipaddress')->from('entities')->where('city', 'like', $post . '%')->order_by('name', 'ASC')->execute()->as_array();
		$this->response->headers('Content-Type', 'application/json');
		$this->response->headers('Cache-Control', 'no-cache');
		if (Request::current()->is_ajax()) $this->response->body(json_encode($query));
		else throw new Kohana_Exception('This controller only accepts AJAX requests', $query);
	}

	public function action_topTenManagers() {
		if ($_POST['name'] == "  " || $_POST['name'] == "topten") {
			$this->auto_render = false;
			$processes = Database::instance()->query(Database::SELECT,"SELECT source_id,count(*) FROM processes GROUP BY source_id ORDER BY count(*)");
			$result = array();
			foreach($processes as $process) {
				$result['entities'][] = Sprig::factory('entity',array('id'=>$process["source_id"]))->load()->as_array();
			}

			$this->response->headers('Content-Type', 'application/json');
			$this->response->headers('Cache-Control', 'no-cache');
			if (Request::current()->is_ajax())
				$this->response->body(json_encode($result));
			else throw new Kohana_Exception('This controller only accepts AJAX requests', $result);
		} else {
			$this->action_list();
		}

	}

    public function action_checkRRD($id) {
        $view = View::factory('entities/checkRRD');
        /**
         * @var Model_Entity
         */
        $entity = Sprig::factory('entity', array('id' => $id))->load();
        $this->template->title .= "Checagem dos Arquivos RRD " . $entity->name;

        $messages = array();
        $sources = $this->sources($entity);
        foreach($sources as $key => $source) {
            $messages[$key] = Pair::instance($key,$id)->checkRRDFiles();
        }

        $view->bind('messages',$messages)->bind('sources',$sources);
        $this->template->content = $view;
    }

    public function action_androidList()
    {
        $this->auto_render = false;
        $entities = Sprig::factory('entity')->load(NULL, FALSE);
        //Fire::group('Models Loaded')->info($entities)->groupEnd();
        $view = View::factory('entities/androidList');

        $view->bind('entities', $entities);
        //$this->setTemplate("templates/empty");

        //$this->template->content = $view;
        $this->request->body($view->render());
        $this->response->body($view->render());
    }

} // End Welcome
