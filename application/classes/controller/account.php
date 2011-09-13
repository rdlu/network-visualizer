<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marcelo
 * Date: 3/31/11
 * Time: 4:18 PM
 * To change this template use File | Settings | File Templates.
 */

//to_do:
//checagem de dias para bloquear a conta OK
//troca de senha de 30 em 30 dias
//transmitir erros para a página (n) OU unir save_changes com edit (n) OU filtra tudo no JS (y)



class Controller_Account extends Controller_Skeleton {

    //public $auth_required = array('login', 'admin');
    
    public $secure_actions = array(
        'register'  =>  'admin',        
        //'edit'      =>  'admin',
        'delete'    =>  'admin',        
        'toogle'    =>  'admin',
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
				'js/dev/account.js',
                                'js/dev/validate.js'
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
                                $privilege = $this->request->post('privilege', null);                                
                                
				$user = $user->create_user($_POST,array('username','password','email'));
				#Add the login role to the user
				$login_role = new Model_Role(array('name' => 'login'));
                                $user->add('roles', $login_role);
                                
                                if($privilege == 'administrador'){
                                    $login_role = new Model_Role(array('name' => 'admin'));
                                    $user->add('roles', $login_role);
                                    $login_role = new Model_Role(array('name' => 'config'));
                                    $user->add('roles', $login_role);                                     
                                }
                                elseif($privilege == 'configurador'){
                                    $login_role = new Model_Role(array('name' => 'config'));
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
                        $username = $this->request->post('username');
                        
                        if($this->check_inactivity($username)) {                             
                             $errors[] = array('class'=>'error','message'=>'Usuário está bloqueado devido à inatividade.'); //works fine
                             $content->bind('errors',$errors);
                        }
			elseif($auth->login($_POST['username'],$_POST['password'])) {
				#redirect to the user account
                                $user = Auth::instance()->get_user();
                                Kohana::$log->add(Log::INFO,"Usuário $user->username fez login em ".date("(d/m/Y) H:i:s", $user->last_login));
                                $this->request->redirect('/');
			}
                        else {
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
            //Fire::info($users);
            $this->template->content = $view;
        }

        public function action_edit(){
            $current_user = Auth::instance()->get_user();
            //Fire::info($id, 'id');
            $action = $this->request->post('action', null);
        //
            /* processa os dados e salva as modificações feitas na conta do usuário */
            if($action == 'save'){
                $id = $this->request->post('id', null); //pega o id do usuario a ser atualizado
                if($id !== null){
                    $username = $this->request->post('username', null);
                    //a conta padrão admin só pode ser modificada por admin
                    if( ($username != 'admin') || ($username == 'admin' && $current_user->username == 'admin') ){
                        $email = $this->request->post('email', null);
                        $password = $this->request->post('password', null);
                        $password_confirm = $this->request->post('password_confirm', null);

                        //execução/save
                        try {
                            $user = ORM::factory('user', $id);

                            //var_dump($current_user->id.' '.$user->id);
                            //ou o usuário está modificando a própria conta ou ele é administrador
                            if( ($current_user->id == $id) ||
                                ($current_user->has('roles', ORM::factory('role', array('name' => 'admin')))) ){
                                //não renomeia o usuário para 'admin'
                                //o usuário é o admin e queremos trocar o username dele mesmo
                                if( ($current_user->username == 'admin' && $id == $current_user->id) ||
                                    ($current_user->username != 'admin' && $user->username == 'admin') ){
                                    //naught
                                }
                                else $user->username = $username;
                                $user->email = $email;
                                if($password != null && $password == $password_confirm){
                                    $user->password = $password;
                                }
                                //somente usuários do grupo administrativo podem alterar permissões de acesso
                                if($current_user->has('roles', ORM::factory('role', array('name' => 'admin'))))
                                    $privilege = $this->request->post('privilege', null);
                                    //privilégios são cumulativos. se a pessoa tem admin, ela ten config. e login.
                                    if($privilege == 'administrador'){
                                        $total_rows = DB::delete('roles_users')->where('user_id','=',$id)->execute(); //deleta todos os níveis de acesso do usuário
                                        $login_role = new Model_Role(array('name' => 'admin'));
                                        $user->add('roles', $login_role);
                                        $login_role = new Model_Role(array('name' => 'config'));
                                        $user->add('roles', $login_role);
                                        $login_role = new Model_Role(array('name' => 'login'));
                                        $user->add('roles', $login_role);
                                    }
                                    elseif($privilege == 'configurador'){
                                        $total_rows = DB::delete('roles_users')->where('user_id','=',$id)->execute(); //deleta todos os níveis de acesso do usuário
                                        $login_role = new Model_Role(array('name' => 'config'));
                                        $user->add('roles', $login_role);
                                        $login_role = new Model_Role(array('name' => 'login'));
                                        $user->add('roles', $login_role);
                                    }
                                    elseif($privilege == 'visualizador'){
                                        $total_rows = DB::delete('roles_users')->where('user_id','=',$id)->execute(); //deleta todos os níveis de acesso do usuário
                                        $login_role = new Model_Role(array('name' => 'login'));
                                        $user->add('roles', $login_role);
                                    }
                                }
                                $user->save();
                                $this->request->redirect('account/index');
                            
                        }
                        catch(Exception $e){
                            $errors[] = array('class'=>'error','message'=>'Usuário está inativo.');
                                        $content->bind('errors',$errors);
                        }
                    }
                }
            } //end of action == save
               /* gera a tela de edição dos dados do usuário */
            else {
                $id = $this->request->query('id');
                if($id !== null){
                    try {
                        $user = ORM::factory('user', $id);
                            $view = View::factory('account/edit')
                                ->bind('user', $user);
                    }
                    catch(ORM_Validation_Exception $e){
                            //Fire::info($e);
                            $errors[] = array('class'=>'error','message'=>'Usuário e/ou senha inválidos.');
                            $view->bind('errors',$errors);
                    }
                    if(! $this->request->is_ajax()){
                        $this->template->content = $view;
                    }
                }
            }
                        
        }
        public function action_toogle(){ //torna o usuário ativo ou inativo, dependendo
            $current_user = Auth::instance()->get_user();
            $id = $this->request->query('id', null);
            if($id != null){
                $user = ORM::factory('user', $id);
                if($user->username != 'admin' || 
                  ($current_user->has('roles', ORM::factory('role', array('name' => 'admin'))))      )
                {
                    //usuário admin é padrão e não pode ser desativado
                    if($user->active == 1){ //desativando
                        $user->active = 0;
                    }
                    else {                  //ativando
                        $user->last_login = 0; //quando reativa o user, reseta o último login. Você pode logar com valor zero.
                        $user->active = 1;      //usuários são criados ativos por default e inativados após 45 dias de inatividade ou por controle de usuários nível admin
                    }
                    $user->save();
                }
                //else não é tratado pq não é para estar disponível na tela do usuário
            }

            if(! $this->request->is_ajax()){
                $this->request->redirect('account/index');
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
                    //Fire::info($e);
                    die();
                }
                if($id != $cur_user->id){ //não deixa o usuário se auto-deletar
                    $user->delete();
                    $this->request->redirect('account/index');
                }
                else {
                    //Fire::info('erro: Usuário não pode deletar a si mesmo');
                    die();
                }
            }
        }

        protected function check_inactivity($username){
            try {
                $user = ORM::factory('user')->where('username', '=', $username)->find();
                //$current_user = Auth::instance()->get_user();
                //$today = time();
                $xxx_dias = (int) strtotime('45 days ago');
                $last_login = (int) $user->last_login; //converte o último login para dias
                
                if($last_login < $xxx_dias && $last_login != 0){ //verifica se é maior do que 45 dias
                    //var_dump($xxx_dias);
                    //var_dump($last_login);
                    //var_dump($user);
                    //die();
                    if($username != 'admin'){
                        $user->active = 0;
                        $user->save();
                    }
                    return true;
                }
                else return false;
            }
            catch(Exception $e){
                return false; //retorna falso significa que o nome não foi encontrado.
                                //Então deixa o programa seguir em frente e testar as próximas condições
                                   //Não retorna false pq o erro é diferente
            }
        }
}


