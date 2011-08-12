<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rodrigo
 * Date: 3/31/11
 * Time: 4:18 PM
 * To change this template use File | Settings | File Templates.
 */

class Controller_Account extends Controller_Skeleton {

	public function before() {
        parent::before();
        $this->template->title .= 'Contas de Usuário :: ';
    }

	public function action_register() {
		#If user already signed-in
		if (Auth::instance()->logged_in() != 0) {
			#redirect to the user account
			Request::current()->redirect('account/myaccount');
		}

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

				#sign the user in
				Auth::instance()->login($user->username, $user->password);

				#redirect to the user account
				Request::current()->redirect('welcome/index');
			} catch (ORM_Validation_Exception $e) {
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
			Request::current()->redirect('welcome/index');
		}

		$content = $this->template->content = View::factory('account/signin');

		#If there is a post and $_POST is not empty
		if ($_POST) {
			$auth = Auth::instance();
			#Instantiate a new user
			//$user = ORM::factory('user');

			if ($auth->login($_POST['username'],$_POST['password'])) {
				#redirect to the user account
				Request::current()->redirect('welcome');
			} else {
				#Get errors for display in view
				$errors[] = array('class'=>'error','message'=>'Usuário e/ou senha inválidos.');
				$content->bind('errors',$errors);
			}

		}
	}


	public function action_signout() {
		#Sign out the user
		Auth::instance()->logout();

		#redirect to the user account and then the signin page if logout worked as expected
		Request::current()->redirect('welcome');
	}

}
