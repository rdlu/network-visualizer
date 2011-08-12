<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Entities extends Controller_Skeleton
{

	public $auth_required = array('login', 'admin');

	// Controls access for separate actions
	// 'adminpanel' => 'admin' will only allow users with the role admin to access action_adminpanel
	// 'moderatorpanel' => array('login', 'moderator') will only allow users with the roles login and moderator to access action_moderatorpanel
	public $secure_actions = FALSE;

	public function before()
	{
		parent::before();
		$this->template->title .= 'Entidades :: ';

		//teste

		//teste2
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

	public function action_remove($id)
	{
		$entity = Sprig::factory('entity');
		if ($id != 0) {
			$entity->id = $id;
			$entity->load();
		}

		if ($entity->loaded()) {
			$view = View::factory('entities/remove');
			$view->set('name', $entity->name);
			$entity->delete();
			$this->template->content = $view;
		} else $this->template->content = 'Entidade não existente no MoM';
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
			foreach ($asSource as $process1) {
				$ass1 = $process1->destination->load()->as_array();
				$assou[$ass1['id']] = $ass1;
			}

			$asDestination = Sprig::factory('process')->load(Db::select()->group_by('source_id')->where('destination_id', '=', $entity->id), null);
			$asdest = array();
			foreach($asDestination as $process2) {

				$asdest[] = $process2->source->load()->as_array();
			}

			$processes = Sprig::factory('process')->load(Db::select()->or_where('source_id', '=', $entity->id)->or_where('destination_id','=',$entity->id), null);


			foreach($processes as $process) {
				$proca[] = $process->as_array();
			}

			$processJSON =Zend_Json::encode($proca);

			$view->bind('entity', $entity)
					->bind('status', $status)
					->bind('destinations', $assou)
					->bind('sources',$asdest)
					->bind('processes',$processes)
					->bind('procJSON',$processJSON);
			$this->template->content = $view;
		} else {
			$this->template->content = 'Entidade não localizada no sistema';
		}
	}

	public function action_byCity()
	{
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

} // End Welcome
