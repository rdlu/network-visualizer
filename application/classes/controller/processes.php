<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Processes extends Controller_Skeleton {

    public function before() {
        parent::before();
        $this->template->title .= 'Processos de Medição :: ';
    }

	public function action_index() {
        $view = View::factory('processes/index');
        $this->template->title .= 'Escolha da Entidade de Origem do Tráfego';

        $entities = Sprig::factory('entity')->load(NULL, FALSE);
        $estados = Sprig::factory('uf')->load(NULL, FALSE);
        $view->bind('entities',$entities);
        $this->template->content = $view;
	}

    public function action_list($sourceAddr=0) {
        $this->auto_render = false;
        $view = View::factory('processes/list');

        if(!$sourceAddr) {
            $sourceAddr = '128.0.0.1';
            $errors[] = "Selecione uma sonda na lista.";
        }

        if(Validate::ip($sourceAddr)){
            $sourceEntity = Sprig::factory('entity',array('ipaddress'=>$sourceAddr))->load();
            $entities = Sprig::factory('entity')->load(NULL, FALSE);

            if($sourceEntity->id) {
                Fire::info(array('source_id'=>$sourceEntity->id));
                $processes = Sprig::factory('process',array('source'=>$sourceEntity->id))->load(NULL,FALSE);
            } else {
                $processes = null;
            }

            Fire::group('Models Loaded')
                    ->info($processes)
                    ->info($sourceEntity)
                    ->info($entities)
                    ->groupEnd();
        } else {
            $errors[] = "A origem $sourceAddr não é um IP válido. Você deve informar um IP válido.";
        }


        $view->bind('processes',$processes)
                ->bind('errors',$errors)
                ->bind('source',$sourceEntity)
                ->bind('sourceAddr',$sourceAddr)
                ->bind('entities',$entities);
        $this->request->response = $view;
	}

    public function action_new($source = 0) {
        $process = Sprig::factory('process');
        $profiles = Sprig::factory('profile')->load(NULL, FALSE);

        if($source!=0) {
            $sourceEnt = Sprig::factory('entity',array('ipaddress'=>$source))->load();
            $process->source = $sourceEnt->id;
            $sourceEntity = $process->source->load();
            Fire::group('Models Loaded',array('Collapsed'=>'true'))->info($process)->info($sourceEntity)->groupEnd();
        }

        if ($_POST) {
            try {
                $process->values($_POST)->create();
            } catch (Validate_Exception $e) {
                $errors = $e->array->errors('processes/edit');
                Fire::group('Form Validation Results')->warn($errors)->groupEnd();
            }
        }
        $view = View::factory('processes/form');
        $view->bind('process',$process)
                ->bind('errors',$errors)
                ->bind('source',$source)
                ->bind('sourceEntity',$sourceEntity)
                ->bind('profiles',$profiles);
        $this->template->content = $view;
    }

    public function action_setup($id) {
        //validando dados
        $validado = true;
        if(!Validate::numeric($_POST['sonda']) && ($_POST['sonda']>0)) $validado = false;


        if ($_POST) {
            $source = $id;
            $destination = $_POST['sonda'];
            $profile = $_POST['profile'];
            $sourceModel = Sprig::factory('entity',array('id'=>$source))->load();
            $destinationModel = Sprig::factory('entity',array('id'=>$destination))->load();
            $profileModel = Sprig::factory('profile',array('id'=>$profile))->load();
            $view = View::factory('processes/setup');
            $view->bind('source',$source)
                    ->bind('destination',$destination)
                    ->bind('profile',$profile)
                    ->bind('pModel',$profileModel)
                    ->bind('dModel',$destinationModel)
                    ->bind('sModel',$sourceModel);

            $this->template->content = $view;
        }
    }

    public function action_setupDestination($id,$sourceId,$profileId) {
        if(Request::$is_ajax) {
            $this->auto_render = false;
            $source = Sprig::factory('entity',array('id'=>$sourceId))->load();
            $destination = Sprig::factory('entity',array('id'=>$id))->load();
            $profile = Sprig::factory('profile',array('id'=>$profileId))->load();

            $values = array(
                'managerEntryStatus' => 6,
                'managerAddress' => $source->ipaddress,
                'managerPort' => 12000,
                'managerProtocol' => 0
            );

            $snmp = Snmp::instance($destination->ipaddress,'suppublic')->setGroup('managerTable',$values,array('id'=>$source->id));

            if(count($snmp)) {
                $e['errors'] = $snmp;
                Kohana::$log->add(Kohana::ERROR,"Erro no SNMP set managerTable para o ip $destination->ipaddress");
                $e['message'] = 'Erros na transmissão dos dados via SNMP';
                $e['class'] = 'error';
            } else {
                $e['errors'] = null;
                $e['message'] = "Entidade de destino $destination->name ($destination->ipaddress) foi configurada com sucesso via SNMP.";
            }

            $this->request->headers['Content-Type'] = 'application/json';
            $this->request->response = json_encode($e);
        }
    }

    public function action_setupSource($id,$destinationId,$profileId) {
        if(Request::$is_ajax) {
            $this->auto_render = false;
            $source = Sprig::factory('entity',array('id'=>$id))->load();
            $destination = Sprig::factory('entity',array('id'=>$destinationId))->load();
            $profile = Sprig::factory('profile',array('id'=>$profileId))->load();

            //checar se o perfil existe e fazer o setup
            try {
                $ps = snmp2_get($source->ipaddress,'suppublic','.1.3.6.1.2.1.1.1.0'.$profile->id);
            } catch (Exception $e) {
                Fire::group('Exception')->error($e)->info($e->getCode(),'Codigo do erro')->groupEnd();
            }

            if(!preg_match('/^No Such/',$ps)) {
                Fire::info($ps);
            } else {
                $values = $profile->as_array();
                $ptable = Snmp::instance($destination->ipaddress,'suppublic')->setGroup('profileTable',$values,array('id'=>$destination->id));

                $avalues = $destination->as_array();
                $atable = Snmp::instance($destination->ipaddress,'suppublic')->setGroup('agentTable',$values,array('id'=>$destination->id));

                if(count($ptable)) {
                    $e['errors'] = array_keys($ptable);
                    Kohana::$log->add(Kohana::ERROR,"Erro no SNMP set Source para o ip $destination->ipaddress");
                    $e['message'] = 'Erros na transmissão dos dados via SNMP (Profile Setup)';
                    $e['class'] = 'error';
                    $ptrue = true;
                }

                if(count($atable)) {
                    Kohana::$log->add(Kohana::ERROR,"Erro no SNMP set Source para o ip $destination->ipaddress");
                    $e['message'] = 'Erros na transmissão dos dados via SNMP (Agent Setup)';
                    $e['class'] = 'error';
                    if(isset($ptrue)) {
                        $e['message'] .= ' & (Profile Setup)';
                        $e['errors'] = array_merge($e['errors'],array_keys($ptable));
                    } else
                        $e['errors'] = array_keys($ptable);
                    $atrue = true;
                }

                if(!isset($ptrue) && !isset($atrue)) {
                    $e['errors'] = null;
                    $e['class'] = 'success';
                    $e['message'] = "Entidade de destino $destination->name ($destination->ipaddress) foi configurada com sucesso via SNMP.";
                }
            }

            $this->request->headers['Content-Type'] = 'application/json';
            $this->request->response = json_encode($e);
        }
    }

    public function action_SaveProfile($profileId,$sourceId,$destinationId) {
        if(Request::$is_ajax) {
            $this->auto_render = false;
            $source = Sprig::factory('entity',array('id'=>$sourceId))->load();
            $destination = Sprig::factory('entity',array('id'=>$destinationId))->load();
            $profile = Sprig::factory('profile',array('id'=>$profileId))->load();

            //checar se o perfil existe e fazer o setup
            try {
                $ps = snmp2_get($source->ipaddress,'suppublic','.1.3.6.1.2.1.1.1.0'.$profile->id);
                if(!preg_match('/^No Such/',$ps)) {
                    $response['message'] = 'Perfil não foi setado corretamente na máquina de origem, as configurações não serão salvas.';
                    $response['class'] = 'error';
                } else {
                    $process = Sprig::factory('process');
                    $process->source = $source;
                    $process->destination = $destination;
                    $process->profile = $profile;
                    $process->create();

                }
            } catch (Exception $e) {
                Fire::group('Exception')->error($e)->info($e->getCode(),'Codigo do erro')->groupEnd();
                $response['message'] = 'Perfil não pode ser enviado para a máquina de origem, as configurações não serão salvas.';
                $response['class'] = 'error';
            }

        }
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
