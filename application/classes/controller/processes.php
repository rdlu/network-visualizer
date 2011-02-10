<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Processes extends Controller_Skeleton {

    public function before() {
        parent::before();
        $this->template->title .= 'Processos de Medição :: ';
    }

	public function action_index() {
        $view = View::factory('processes/index');
        $this->template->title .= 'Escolha da Entidade de Origem do Teste';

        $entities = Sprig::factory('entity')->load(NULL, FALSE);
        $estados = Sprig::factory('uf')->load(NULL, FALSE);
        $view->bind('entities',$entities);
        $this->template->content = $view;
	}

    public function action_list($sourceAddr=0) {
        $this->auto_render = false;
        $view = View::factory('processes/list');
        $this->template->title .= 'Escolha da Entidade de Destino do Teste';

        if(!$sourceAddr) {
            $sourceAddr = '128.0.0.1';
            $errors[] = "Selecione uma sonda na lista.";
        }

        if(Validate::ipOrHostname($sourceAddr)){
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

    /**
     * SETUP de NOVO PROCESSO
     * O Setup de um novo processo é feito em 3 partes
     */
    /**
     * Funcao que mostra o formulario de escolha de destino, baseado na origem.
     * @param int $source
     * @return void
     */
    public function action_new($source = 0) {
	    $this->template->title .= 'Criando novo processo de medição';
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
                $process->check($_POST);
                $this->request->redirect($this->request->controller.'/setup/'.$process->source->id);
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

    /**
     * Funcao que inicializa o setup do processo, apos o formulario ser enviado
     * @param  $id
     * @return void
     */
    public function action_setup($source) {
	    $this->template->title .= 'Configurando o novo processo de medição';
        //validando dados
        $validado = false;
        if(Validate::numeric($_POST['sonda']) && ($_POST['sonda']>0))
            if(Validate::numeric($_POST['profile']) && ($_POST['profile']>0)) $validado = true;
        $view = View::factory('processes/setup');

        if ($validado) {
            $source = (int) $source;
            $destination = $_POST['sonda'];
            $profile = $_POST['profile'];

				$process = self::setupFirst($source,$destination,$profile);


				$destination = $process->destination->load();
				$source = $process->source->load();
				$profile = $process->profile->load();
				$errors = false;

			   $view->bind('process',$process)
					   ->bind('destination',$destination)
					   ->bind('source',$source)
					   ->bind('profile',$profile)->bind('errors',$errors);
        } else {
	        throw new Exception('Validation error on Process Setup, non numeric data sent',23);
        }
        $this->template->content = $view;
    }

	/**
	 * @static
	 * @param  string $source
	 * @param  string $destination
	 * @param  string $profile
	 * @return bool|Model_Process|Sprig
	 */
	private static function setupFirst($source,$destination,$profile) {
		$sourceModel = Sprig::factory('entity',array('id'=>$source))->load();
      $destinationModel = Sprig::factory('entity',array('id'=>$destination))->load();
      $profileModel = Sprig::factory('profile',array('id'=>$profile))->load();

      $process = Sprig::factory('process');
      $process->source = $sourceModel;
      $process->destination = $destinationModel;
      $process->profile = $profileModel;

		$q = Db::select('port')->from('processes')->where('source_id','=',$source)->where('destination_id','=',$destination)->order_by('port','DESC')->execute();
		if($q->count())
			$process->port = $q->get('port') + 1;
		else
			$process->port = 12000;

      try {
	      $process->create();
      } catch (Exception $e) {
	      $msg = $e->getMessage();
	      $error['message'] = "Exceção no banco de dados, $msg";
	      $error['class'] = 'error';
      }

	   return Sprig::factory('process',array('source'=>$source,'destination'=>$destination,'profile'=>$profile))->load();
	}

    public function action_setupDestination($processId) {
        if(Request::$is_ajax) {
            $this->auto_render = false;
            $process = Sprig::factory('process',array('id'=>$processId))->load();
            $source = $process->source->load();
            $destination = $process->destination->load();
            $profile = $process->profile->load();

            $values = array(
                'managerEntryStatus' => 6,
                'managerAddress' => $source->ipaddress,
                'managerPort' => $process->port,
                'managerProtocol' => $profile->protocol
            );

            $snmp = Snmp::instance($destination->ipaddress,'suppublic')->setGroup('managerTable',$values,array('id'=>$process->id));

            if(count($snmp)) {
                $e['errors'] = $snmp;
                Kohana::$log->add(Kohana::ERROR,"Erro no SNMP set managerTable para o ip $destination->ipaddress");
                $e['message'] = 'Erros na transmissão dos dados via SNMP: ';
                $e['class'] = 'error';
            } else {
                $e['errors'] = null;
                $e['message'] = "Entidade de destino $destination->name ($destination->ipaddress) foi configurada com sucesso via SNMP.";
                $e['class'] = 'success';
            }

            $this->request->headers['Content-Type'] = 'application/json';
            $this->request->response = json_encode($e);
        }
    }

	public function action_setupSource($processId) {
		if(Request::$is_ajax) {
			$this->auto_render = false;
			$process = Sprig::factory('process',array('id'=>$processId))->load();
			$source = $process->source->load();
			$destination = $process->destination->load();
			$profile = $process->profile->load();

			//Checar se o destino esta OK
			$sourceSnmp = Snmp::instance($source->ipaddress,'suppublic');

		      if(!$sourceSnmp->isReachable(NMMIB.'.1.0.9.'.$process->id)) {
			      $values = array('entryStatus'=>6);
					$values = array_merge($values,$profile->as_array());
			      $values['gap'] = $profile->gap * 1000;
					$values['metrics'] = $profile->metrics;
					$ptable = $sourceSnmp->setGroup('profileTable',$values,array('id'=>$profile->id));
		      } else {
			      $ptable = array();
		      }
		      
		      $avalues = array('entryStatus'=>6);
            $avalues = array_merge($avalues,$destination->as_array());
            $avalues['profile'] = $profile->id;
				$avalues['port'] = $process->port;
            $atable = $sourceSnmp->setGroup('agentTable',$avalues,array('pid'=>$process->id));

            if(count($ptable)) {
	            $e['errors'] = array_keys($ptable);
	            Kohana::$log->add(Kohana::ERROR,"Erro no SNMP set Source para o ip $source->ipaddress (Profile Table)");
	            $e['message'] = 'Erros na transmissão dos dados via SNMP (Profile Setup)';
	            $e['class'] = 'error';
	            $ptrue = true;
            }

            if(count($atable)) {
	            Kohana::$log->add(Kohana::ERROR,"Erro no SNMP set Source para o ip $source->ipaddress (Agent Table)");
	            $e['message'] = 'Erros na transmissão dos dados via SNMP (Agent Setup)';
	            $e['class'] = 'error';
	            if(isset($ptrue)) {
		            $e['message'] .= ' & (Profile Setup)';
		            $e['errors'] = array_merge($e['errors'],array_keys($ptable));
	            } else
		            $e['errors'] = array_keys((array) $ptable);
	            $atrue = true;
            }

            if(!isset($ptrue) && !isset($atrue)) {
	            $e['errors'] = null;
	            $e['class'] = 'success';
	            $e['message'] = "Entidade de origem $source->name ($source->ipaddress) foi configurada com sucesso via SNMP.";
            }

	      $this->request->headers['Content-Type'] = 'application/json';
	      $this->request->response = json_encode($e);
		}
	}

	public function action_FinalCheck($processId) {
        if(Request::$is_ajax) {
	        $this->auto_render = false;
	        $process = Sprig::factory('process',array('id'=>$processId))->load();
	        $source = $process->source->load();
	        $destination = $process->destination->load();
	        $profile = $process->profile->load();

           if($process->count())
	        if(Snmp::instance($source->ipaddress)->isReachable(NMMIB.'.0.0.0.'.$process->id)) {
		        if(Snmp::instance($destination->ipaddress)->isReachable(NMMIB.'.10.0.0.'.$process->id)) {
			        $response['message'] = "Configurações salvas com sucesso no banco de dados do MoM";
			        $response['class'] = 'success';
			        $rrd = Rrd::instance($source->ipaddress,$destination->ipaddress);

			        foreach($profile->metrics as $metric) {
				        $rrd->create($profile->id,$metric->name,$profile->polling);
			        }

			        if($rrd->errors) {
				        $response['message'] .= ', mas houveram falhas na criação dos arquivos RRD, cheque o Registro de Eventos.';
				        $response['class'] = 'warn';
			        }
		        } else {
			        $response['message'] = "A sonda de destino $destination->ipaddress não respondeu ao teste de verificação, abortando a configuração";
			        $response['class'] = 'error';
		           $process->delete();
		        }
	        } else {
		        $response['message'] = "A sonda de origem $source->ipaddress não respondeu ao teste de verificação, abortando a configuração.";
              $response['class'] = 'error';
              $process->delete();
	        }
            $this->request->headers['Content-Type'] = 'application/json';
            $this->request->response = json_encode($response);

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
