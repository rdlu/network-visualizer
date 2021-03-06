<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Processes_Exception extends Exception
{
}

class Controller_Processes extends Controller_Skeleton
{

    public $auth_required = array('login', 'admin');

    // Controls access for separate actions
    // 'adminpanel' => 'admin' will only allow users with the role admin to access action_adminpanel
    // 'moderatorpanel' => array('login', 'moderator') will only allow users with the roles login and moderator to access action_moderatorpanel
    public $secure_actions = array('new' => 'admin', 'setup' => 'admin', 'remove' => 'admin');

    public function before()
    {
        parent::before();
        $this->template->title .= 'Processos de Medição :: ';
    }

    public function action_index()
    {
        $view = View::factory('processes/index');
        $this->template->title .= 'Escolha da Entidade de Origem do Teste';

        $entities = ORM::factory('entity')->find_all();

        $view->bind('entities', $entities)->set('defaultManager', Sonda::getDefaultManager());
        $this->template->content = $view;
    }

    public function action_list()
    {
        $sourceAddr = $this->request->param('source', 0);
        $this->auto_render = false;
        $view = View::factory('processes/list');
        $this->template->title .= 'Escolha da Entidade de Destino do Teste';

        if (!$sourceAddr) {
            $sourceAddr = '128.0.0.1';
            $errors[] = "Selecione uma sonda na lista.";
        }

        if (Valid::ipOrHostname($sourceAddr)) {
            $sourceEntity = ORM::factory('entity')->where('ipaddress', '=', $sourceAddr)->find();
            $processes = ORM::factory('process')->where('source_id', '=', $sourceEntity->id)->find_all();

            foreach ($processes as $process) {
                $destination = $process->destination;
                $destinations[$destination->id] = $destination;
                $pair = Pair::instance($sourceEntity->id, $destination->id);
                $metrics[$destination->id] = $pair->getMetrics();
            }

        } else {
            $errors[] = "A origem $sourceAddr não é um IP válido. Você deve informar um IP válido.";
        }


        $view->bind('processes', $processes)
            ->bind('errors', $errors)
            ->bind('source', $sourceEntity)
            ->bind('sourceAddr', $sourceAddr)
            ->bind('metrics', $metrics)
            ->bind('destinations', $destinations);
        $this->response->body($view);
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
    public function action_new()
    {
        $source = $this->request->param('source');
        $test = $this->request;
        $this->template->title .= 'Criando novo processo de medição';
        $process = ORM::factory('process');
        $profiles = ORM::factory('profile')->find_all();
        $thresholds = ORM::factory('thresholdProfile')->find_all();

        if ($source) {
            $sourceEntity = ORM::factory('entity', $source);
            $process->source = $sourceEntity;
        } else throw new Controller_Processes_Exception("Falta um parametro obrigatório, a sonda de origem", 5);

        $metrics = ORM::factory('metric')->where('profile_id', 'IS NOT', null)->order_by('order')->find_all();

        if ($_POST) {
            try {
                $process->check($_POST);
                $this->request->redirect($this->request->controller() . '/setup/' . $process->source->id);
            } catch (Validation_Exception $e) {
                $errors = $e->array->errors('processes/edit');
            }
        }

        $measure = Kohana::$config->load('measure');
        $conversion = new conversion();

        foreach ($thresholds as $threshold) {
            $tvalues = $threshold->thresholdValues->find_all();
            $tarr = array();
            foreach ($tvalues as $tvalue) {
                $tmet = $tvalue->metric;
                $order[] = $tmet->order;
                $tarr[$tmet->name] = $tvalue->as_array();
                $tarr[$tmet->name]['reverse'] = (int)$tmet->reverse;
                $type = $measure[$tmet->name]['type'];

                //Faz a conversao dos valores em bps/segundos para um formato mais humano (Kbps/Mbps/ms)
                if ($type) {
                    $tarr[$tmet->name]['max'] = $conversion->{$type}($tarr[$tmet->name]['max']);
                    $tarr[$tmet->name]['min'] = $conversion->{$type}($tarr[$tmet->name]['min']);
                } else {
                    //Caso nao precise de conversao só concatena a unidade de medida
                    $tarr[$tmet->name]['max'] = $tarr[$tmet->name]['max'] . " " . $measure[$tmet->name]['default'];
                    $tarr[$tmet->name]['min'] = $tarr[$tmet->name]['min'] . " " . $measure[$tmet->name]['default'];
                }
            }
            array_multisort($order, $tarr);
            $tvals[$threshold->id] = $tarr;
        }


        $view = View::factory('processes/form');
        $view->bind('process', $process)
            ->bind('errors', $errors)
            ->bind('source', $source)
            ->bind('sourceEntity', $sourceEntity)
            ->bind('profiles', $profiles)
            ->bind('thresholds', $thresholds)
            ->bind('thresholdsValues', $tvals)
            ->bind('metrics', $metrics);
        $this->template->content = $view;
    }

    /**
     * Funcao que inicializa o setup do processo, apos o formulario ser enviado
     * @param  $id
     * @return void
     */
    public function action_setup()
    {
        $source = $this->request->param('source');
        $this->template->title .= 'Configurando o novo processo de medição';
        //validando dados
        $validado = false;
        if (Valid::numeric($_POST['destination']) && ($_POST['destination'] > 0)) $validado = true;
        $view = View::factory('processes/setup');

        if ($validado) {
            $source = (int)$source;
            $destination = (int)$_POST['destination'];
            $metrics = (array)$_POST['metrics'];
            $thresholdProfile = (int)$_POST['threshold'];

            $rows = DB::select()->from('metrics')->group_by('profile_id')->where('profile_id', "!=", null)->or_where('id', 'IN', $metrics)->execute();

            $profiles = array();
            foreach ($rows as $row) {
                $profiles[] = ORM::factory('profile', $row['profile_id']);
            }

            $sourceModel = ORM::factory('entity', $source);
            $destinationModel = ORM::factory('entity', $destination);
            $thresholdModel = ORM::factory('thresholdProfile', $thresholdProfile);

            $errors = false;

            foreach ($profiles as $k => $profile) {
                $process = ORM::factory('process');
                $process->source = $sourceModel;
                $process->destination = $destinationModel;
                $process->profile = $profile;
                $process->thresholdProfile = $thresholdModel;

                try {
                    $process->save();
                    //se salvo com sucesso, relaciona
                    $process->add('metrics', $metrics);
                    $resultModels[$k] = $process->reload();
                    $resultIds[$k] = $resultModels[$k]->id;
                } catch (Exception $e) {
                    $msg = $e->getMessage();
                    $errors[$k]['message'] = "Exceção no banco de dados, $msg";
                    $errors[$k]['class'] = 'error';
                }

            }

            $view->bind('process', $process)->bind('processes', $resultModels)->bind('processIDs', $resultIds)
                ->bind('destination', $destinationModel)
                ->bind('source', $sourceModel)
                ->bind('profiles', $profiles)->bind('errors', $errors);
        } else {
            throw new Exception('Validation error on Process Setup, non numeric data sent', 23);
        }
        $this->template->content = $view;
    }

    public function action_setupDestination()
    {
        if (Request::current()->is_ajax()) {
            $this->auto_render = false;
            $procs = (array)$_POST['processes'];

            foreach ($procs as $i => $proc) {
                $process = ORM::factory('process', $proc);
                //Fire::info($process->as_array(),"Process $i to be configured. ID: $proc");
                $source = $process->source;
                $destination = $process->destination;

                if ($destination->isAndroid) {
                    $e['errors'] = null;
                    $e['message'] = "Entidade de destino $destination->name ($destination->ipaddress) tem o sistema Android e dispensa configurações adicionais.";
                    $e['class'] = 'success';
                    break;
                }
                $profile = $process->profile;
                $values = array(
                    'managerEntryStatus' => 6,
                    'managerAddress' => $source->ipaddress,
                    'managerPort' => 12000 + $profile->id,
                    'managerProtocol' => $profile->protocol
                );

                //Fire::info($values,"To do on Manager Table");

                $snmp = Snmp::instance($destination->ipaddress, 'suppublic')->setGroup('managerTable', $values, array('id' => $process->id));

                if (count($snmp)) {
                    $e['errors'] = $snmp;
                    Kohana::$log->add(Log::ERROR, "Erro no SNMP set managerTable para o ip $destination->ipaddress");
                    $e['message'] = 'Erros na transmissão dos dados via SNMP: ';
                    $e['class'] = 'error';
                    break;
                } else {
                    $e['errors'] = null;
                    $e['message'] = "Entidade de destino $destination->name ($destination->ipaddress) foi configurada com sucesso via SNMP.";
                    $e['class'] = 'success';
                }

            }


            $this->response->headers('Content-Type', 'application/json');
            $this->response->body(json_encode($e));
        }
    }


    public function action_setupSource()
    {
        if (Request::current()->is_ajax()) {
            $this->auto_render = false;
            $procs = (array)$_POST['processes'];

            $pair = Pair::instanceFromProcess(current($procs));
            $source = $pair->getSource();
            $destination = $pair->getDestination();

            foreach ($procs as $i => $proc) {
                if ($pair->setProfile($proc)) {
                    if ($pair->setAgent($proc)) {
                        $e['errors'] = null;
                        $e['class'] = 'success';
                        $e['message'] = "Entidade de origem $source->name ($source->ipaddress) foi configurada com sucesso via SNMP.";
                    } else {
                        Kohana::$log->add(Log::ERROR, "Erro no SNMP set Source para o ip $source->ipaddress (Agent Table)");
                        $e['message'] = 'Erros na transmissão dos dados via SNMP (Agent Setup)';
                        $e['class'] = 'error';
                    }
                } else {
                    Kohana::$log->add(Log::ERROR, "Erro no SNMP set Source para o ip $source->ipaddress (Profile Table)");
                    $e['message'] = 'Erros na transmissão dos dados via SNMP (Profile Setup)';
                    $e['class'] = 'error';
                    break;
                }
            }

            $this->response->headers('Content-Type', 'application/json');
            $this->response->body(json_encode($e));
        }
    }

    public function action_FinalCheck()
    {
        if (Request::current()->is_ajax()) {
            $this->auto_render = false;
            $procs = (array)$_POST['processes'];

            foreach ($procs as $i => $proc) {
                $process = ORM::factory('process', $proc);
                /**
                 * @var Model_Entity
                 */
                $source = $process->source;
                /**
                 * @var Model_Entity
                 */
                $destination = $process->destination;

                $profile = $process->profile;
                if (Snmp::instance($source->ipaddress)->isReachable(NMMIB . '.0.0.0.' . $process->id)) {
                    if ($destination->isAndroid || Snmp::instance($destination->ipaddress)->isReachable(NMMIB . '.10.0.0.' . $process->id)) {
                        $source->status = 1;
                        $source->update();
                        $destination->status = 2;
                        $destination->update();
                        $response['message'] = "Configurações salvas com sucesso no banco de dados do MoM";
                        $response['class'] = 'success';

                        if (!$destination->isAndroid) {
                            $rrd = Rrd::instance($source->ipaddress, $destination->ipaddress);

                            foreach ($profile->metrics as $metric) {
                                $rrd->create($metric->name, $profile->polling);
                            }

                            if ($rrd->errors) {
                                $response['message'] .= ', mas houveram falhas na criação dos arquivos RRD, cheque o Registro de Eventos.';
                                $response['class'] = 'warn';
                            }
                        }
                    } else {
                        $response['message'] = "A sonda de destino $destination->ipaddress não respondeu ao teste de verificação, abortando a configuração";
                        $response['class'] = 'error';
                        $values['entryStatus'] = 2;
                        $sourceSnmp = Snmp::instance($source->ipaddress, 'suppublic')->setGroup('removeAgent', $values, array('id' => $process->id));
                        $process->delete();
                        //break;
                    }
                } else {
                    $response['message'] = "A sonda de origem $source->ipaddress não respondeu ao teste de verificação, abortando a configuração.";
                    $response['class'] = 'error';
                    $process->delete();
                    //break;
                }
            }

            $this->response->headers('Content-Type', 'application/json');
            $this->response->body(json_encode($response));

        }
    }

    public function action_remove()
    {
        if (Request::current()->is_ajax()) {
            $this->auto_render = false;

            $source = (int)$_POST['source'];
            $destination = (int)$_POST['destination'];
            $force = (boolean)(isset($_POST['force'])) ? $_POST['force'] : false;

            $pair = Pair::instance($source, $destination);
            $responses = $pair->removeProcesses($force);

            //procura se eh a ultima medicao removida no destino
            $db = Db::select()->from('processes')->or_where('source_id', '=', $destination)->or_where('destination_id', '=', $destination)->execute();

            if ($db->count() == 0) {
                $sou = ORM::factory('entity', $destination);
                $sou->status = 0;
                $sou->update();
            }

            $this->response->headers('Content-Type', 'application/json');
            $this->response->body(json_encode($responses));
        }
    }

    public function action_view()
    {
        $id = (int)$this->request->param('id');
        ;
        $view = View::factory('process/view');

        $entity = ORM::factory('entity', $id);

        if ($entity->loaded()) {
            $view->bind('entity', $entity);
            $this->template->content = $view;
        } else {
            $this->template->content = 'Entidade não localizada no sistema';
        }
    }

} // End Welcome
