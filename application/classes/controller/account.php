<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rodrigo
 * Date: 3/31/11
 * Time: 4:18 PM
 * To change this template use File | Settings | File Templates.
 */

class Controller_Account extends Controller_Skeleton {

    //public $auth_required = array('login', 'admin');
    
    public $secure_actions = array(
        'register' => 'admin',
        'index' => 'admin',
        'edit' => 'admin',
        'delete' => 'admin',
        'save_changes' => 'admin'
    );

    public function before() {
        parent::before();
        $this->template->title .= 'Contas de Usuário :: ';
    }

    public function after() {
		if ($this->auto_render) {
			$styles = array(
				'css/account.css' => 'all',
			);

			$scripts = array(
				'js/dev/account.js'
			);

			$this->template->styles = array_merge($styles,$this->template->styles);
			$this->template->scripts = array_merge($scripts,$this->template->scripts);
		}
		parent::after();
	}

	public function action_register() {
		//If user already signed-in :: modificando logica @ 21/06
            /*
		if (Auth::instance()->logged_in() != 0) {
			#redirect to the user account
			Request::current()->redirect('account/myaccount');
		}
            */
		#Load the view
		$content = $this->template->content = View::factory('account/register');

		#If there is a post and $_POST is not empty
		if ($_POST) {
			$auth = Auth::instance();
			#Instantiate a new user
			/**
			 * @var Model_User
			 */
			$user = ORM::factory('user');

			try {
				$user = $user->create_user($_POST,array('username','password','email'));
				#Add the login role to the user
				$login_role = new Model_Role(array('name' => 'login'));
				$user->add('roles', $login_role);
                                if($this->request->post('admin', false)){
                                    $login_role = new Model_Role(array('name' => 'admin'));
                                    $user->add('roles', $login_role);
                                }
				#sign the user in
				Auth::instance()->login($user->username, $user->password);

				#redirect to the user account
				$this->request->redirect('account/index');
			}
                        catch (ORM_Validation_Exception $e) {
				$content->errors = $e->errors('account/register');
				//Fire::info($e->errors());
			}
		}
	}


	public function action_signin() {
		$this->template->title .= 'Login';
		#If user already signed-in
		if (Auth::instance()->logged_in() != 0) {
			#redirect to the user account
			Request::current()->redirect('/');
		}

		$content = $this->template->content = View::factory('account/signin');

		#If there is a post and $_POST is not empty
		if ($_POST) {
			$auth = Auth::instance();
			#Instantiate a new user
			//$user = ORM::factory('user');

			if ($auth->login($_POST['username'],$_POST['password'])) {
				#redirect to the user account
				Request::current()->redirect('/');
			} else {
				#Get errors for display in view
				$errors[] = array('class'=>'error','message'=>'Usuário e/ou senha inválidos.');
				$content->bind('errors',$errors);
			}

		}
	}

        public function action_noaccess(){
            $view = View::factory('account/noaccess');
        }

	public function action_signout() {
		#Sign out the user
		Auth::instance()->logout();

		#redirect to the user account and then the signin page if logout worked as expected
		Request::current()->redirect('/');
	}

        public function action_index(){
            $this->template->title .= 'Controle de usuários';
            $users = ORM::factory('user')->find_all();
            $view = View::factory('account/index')
                    ->bind('users', $users);
            Fire::info($users);
            $this->template->content = $view;
        }

        public function action_edit(){
            $id = $this->request->query('id', null);
            //Fire::info($id, 'id');
            if($id != null){ //carrega a página de edição
                try {
                    $user = ORM::factory('user', $id);
                    $view = View::factory('account/edit')
                        ->bind('user', $user);       
                }
                catch(ORM_Validation_Exception $e){
                    Fire::info($e);
                    die();
                }               
                if(! $this->request->is_ajax()){
                    $this->template->content = $view;
                }
            }            
        }

        public function action_savechanges(){
            $id = $this->request->post('id', null);                                    
            if($id != null){
                $username = $this->request->post('username', null);                
                $email = $this->request->post('email', null);
                $password = $this->request->post('password', null);
                $password_confirm = $this->request->post('password_confirm', null);
                try {
                    $user = ORM::factory('user', $id);
                    $user->username = $username;
                    $user->email = $email;
                    if($password != null){                        
                        $user->password = $password;                        
                    }
                    $user->save();
                    $this->request->redirect('account/index');
                }
                catch(Exception $e){
                    var_dump($e->getMessage());
                    //die();
                }
            }
        }

        public function action_delete(){
            $id = $this->request->query('id', null);
            $cur_user = Auth::instance()->get_user();

            if($id != null){
                try {
                    $user = ORM::factory('user', $id);
                }
                catch(ORM_Validation_Exception $e){
                    Fire::info($e);
                    die();
                }
                if($id != $cur_user->id){ //não deixa o usuário se auto-deletar
                    $user->delete();
                    $this->request->redirect('account/index');
                }
                else {
                    Fire::info('erro: Usuário não pode deletar a si mesmo');
                }
            }
        }
}
