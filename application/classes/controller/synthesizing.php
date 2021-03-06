<?php defined('SYSPATH') or die('No direct script access.');
/* controller para a aba Sintetização */
class Controller_Synthesizing extends Controller_Skeleton
{

    public $auth_required = array('login');

    // Controls access for separate actions
    // 'adminpanel' => 'admin' will only allow users with the role admin to access action_adminpanel
    // 'moderatorpanel' => array('login', 'moderator') will only allow users with the roles login and moderator to access action_moderatorpanel
    public $secure_actions = FALSE;

    public function before()
    {
        parent::before();
        $this->template->title .= "Sintetização";
    }

    public function action_index()
    {
        $view = View::factory('synthesizing/index');
        $processes = ORM::factory('process')->group_by('source_id')->find_all();
        $resp = array();
        foreach ($processes as $process) {
            $resp[] = $process->source->as_array();
        }
        //Fire::info($resp, 'Array com os dados de origem: ');

        $view->bind('resp', $resp);
        $this->template->content = $view;
    }

    public function after()
    {

        if ($this->auto_render) {

            $scripts = array(
                'js/dev/jquery.qtip-1.0.0-rc3.js',
                'js/dev/synthesizing.js'
            );
            $this->template->scripts = array_merge($scripts, $this->template->scripts);
        }
        parent::after();
    }

    /*
    * Recebe o id de uma sonda e
    * Retorna as informações JSON sobre as sondas de destino
    */
    public function action_destsondas()
    {
        $source = (int)$this->request->param('source');

        $this->auto_render = false;

        $source = ORM::factory('entity', $source);
        $processes = ORM::factory('process')->group_by('destination_id')
            ->where('source_id', '=', $source->id)->find_all();
        $resp = array();
        foreach ($processes as $process) {
            $resp[] = $process->destination;
        }

        $resFromMemCache = null;
        foreach ($resp as $destination) {
            //Resultados do MemCached
            $resFromMemCache = Kohana_Cache::instance('memcache')->get("$source->id-$destination->id");
        }

        if (Request::current()->is_ajax()) $this->response->headers('Content-Type', 'application/json');
        $this->response->body(Zend_Json::encode($resFromMemCache));


    }

//recebe o id da sonda de origem e retorna informações sobre a sonda de origem
    public function action_origsondas()
    {
        $idorigem = (int)$this->request->param('source');
        if (Request::current()->is_ajax()) {
            $this->auto_render = false;
            $sonda_origem = ORM::factory('entity', $idorigem);

            $JSONresponse = array(
                "id" => $sonda_origem->id,
                "ipaddress" => $sonda_origem->ipaddress,
                "name" => $sonda_origem->name,
                "status" => $sonda_origem->status
            );
            $this->response->headers('Content-Type', 'text/json');
            $this->response->body(JSON_encode($JSONresponse));
        }
    }

    public function action_popup()
    {
        $sondaOrigemId = (int)$this->request->param('source');
        $view = View::factory('synthesizing/popup');
        $view->bind('sondaOrigemId', $sondaOrigemId);
        $this->template->content = $view;
    }

    public function action_Modal()
    {
        $sId = (int)$this->request->param('source', 0);
        $dId = (int)$this->request->param('destination', 0);
        $relative = $this->request->param('relative', false);
        $view = View::factory('synthesizing/modal');
        if (Request::current()->is_ajax()) {
            $this->auto_render = false;
            $sId = (int)$_POST['source'];
            $dId = (int)$_POST['destination'];
            $relative = isset($_POST['relative']) ? $_POST['relative'] : "7 days";
        }

        $inicio = strtotime("-" . $relative);
        //Fire::error($inicio,"$relative");
        $fim = date("U");

        $processes = ORM::factory('process')->where('destination_id', '=', $dId)
            ->where('source_id', '=', $sId)->find_all();
        $source = ORM::factory('entity', $sId);
        $destination = ORM::factory('entity', $dId);

        //Gerando os graficos
        $rrd = Rrd::instance($source->ipaddress, $destination->ipaddress);
        $count = $processes->count();

        //Fire::group("Report status for $source->ipaddress to $destination->ipaddress",array('Collapsed'=>'true'))->info("Number of processes: $count");

        if ($count) {
            $metrics2 = array();

            foreach ($processes as $process) {
                //Fire::info($process->as_array(),"Process 1, ID: $process->id");
                $profile = $process->profile;
                $metrics = $profile->metrics;
                foreach ($metrics as $metric) {
                    $img[$metric->name] = $rrd->graph($metric->name, $inicio, $fim);
                    $metrics2[$metric->name] = array('order' => $metric->order, 'desc' => $metric->desc);
                    $order[] = $metric->order;
                }
            }

            array_multisort($order, $metrics2);

            //Fire::group("Images path")->info($img)->groupEnd();
            //Fire::groupEnd();

            $view->bind('images', $img)
                ->bind('metrics', $metrics2)
                ->bind('processes', $processes)
                ->bind('source', $source)
                ->bind('destination', $destination)
                ->set('header', true); //isset($_POST['relative'])
            if (Request::current()->is_ajax()) {
                $this->response->headers('Cache-Control', "no-cache");
                $this->response->body($view);
            } else {
                $this->template->content = $view;
            }

        } else {
            $this->template->content = "Não há nenhum processo de medição entre $source->name ($source->ipaddress) e $destination->name ($destination->ipaddress)";
        }
    }
}