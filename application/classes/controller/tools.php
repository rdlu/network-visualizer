<?php

class Controller_Tools extends Controller
{

    public function action_check()
    {
        if (!isset($_POST['ip'])) throw new Kohana_Exception('Compulsory data not set, must be called with post', $_POST);

        $ip = $_POST['ip'];
        if (Valid::ipOrHostname($ip)) {
            try {
                $realip = Network::getAddress($ip);
                $data = Snmp::instance($realip)->group('linuxManager');
                $json['address'] = $realip;
                $json['data'] = $data;
            } catch (Network_Exception $err) {
                if ($err->getCode() == 1) {
                    $json['error'] = 'O servidor DDNS não retornou nenhum endereço IP, verifique se a sonda está corretamente registrada no DDNS..';
                } elseif ($err->getCode() == 2) {
                    $json['error'] = 'O servidor DDNS não respondeu. Verifique se o mesmo está OK.';
                }
            }

            $this->response->headers('Content-Type', 'application/json');
            if (Request::current()->is_ajax()) $this->response->body(json_encode($json));
            else throw new Kohana_Exception('This controller only accepts AJAX requests', $json);
        } else {
            throw new Kohana_Exception("Invalid format for IP Address $ip");
        }
    }

    public function action_cities()
    {
        if (!isset($_POST['startsWith'])) throw new Kohana_Exception('Compulsory data not set, must be called with post', $_POST);
        $post = 'Port';
        if (isset($_POST['startsWith'])) $post = (string)$_POST['startsWith'];
        $query['geonames'] = Db::select('city', 'state')->from('entities')->where('city', 'like', $post . '%')->group_by('city')->order_by('city', 'ASC')->execute()->as_array();
        $this->response->headers('Content-Type', 'application/json');
        if (Request::current()->is_ajax()) $this->response->body(json_encode($query));
        else throw new Kohana_Exception('This controller only accepts AJAX requests', $query);
    }

    public function action_entityStatus()
    {
        $id = (int)$this->request->param('id');
        if (Request::current()->is_ajax()) {
            $this->auto_render = false;
            $dados = ORM::factory('entity', $id);
            $this->response->headers('Content-Type', 'application/json');

        }
    }

    public function action_createResultTables()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'text/plain');
        $profiles = ORM::factory('profile')->find_all();
        $metrics = ORM::factory('metric')->find_all();

        foreach ($profiles as $profile) {
            foreach ($metrics as $metric) {
                $model = Model_Results::factory($profile->id, $metric->id)->createDB();
                echo $metric->name . "-" . $profile->id . " \n";
            }
        }
    }

    public function action_testSQL()
    {
        $process_id = (int)$this->request->param('process_id');
        $metricPlugin = (int)$this->request->param('plugin_name');

        $this->response->headers('Content-Type', 'text/plain');
        $process = ORM::factory('process', $process_id);

        if (!$process->loaded()) {
            echo "Process $process_id does not exist.\n";
        }

        $destination = $process->destination;
        $source = $process->source;
        $profile = $process->profile;
        $metric = ORM::factory('metric')->where('plugin', '=', $metricPlugin)->find();

        $toBeSQLed = array(
            'dsmax' => lcg_value() + rand(0, 10),
            'dsmin' => lcg_value() + mt_rand(0, 10),
            'dsavg' => lcg_value() + mt_rand(0, 10),
            'sdmax' => lcg_value() + rand(0, 10),
            'sdmin' => lcg_value() + rand(0, 10),
            'sdavg' => lcg_value() + mt_rand(0, 10),
            'timestamp' => date("U"),
            'stored' => date("U"),
            'process_id' => $process->id,
            'source_name' => $source->name,
            'destination_name' => $destination->name,

        );

        var_dump($toBeSQLed);
        Model_Results::factory($profile->id, $metric->id)->insert($process->id, $toBeSQLed);

    }

    public function action_hashz0r()
    {
        $str = $this->request->param('id');

        echo Kohana_Auth::instance()->hash("VivOGparC,.");
        echo "<br />  ";
        echo Kohana_Auth::instance()->hash($str);
    }

    public function action_test()
    {
        $ent = ORM::factory('entity', $this->request->param('id', null));
        $dests = $ent->destinations->find_all();
        $srcs = $ent->sources->find_all();
        $procs = $ent->processes_as_source->find_all();
        $procd = $ent->processes_as_destination->find_all();
        $test = $procs->current()->source;
        echo "Test";

    }
}
