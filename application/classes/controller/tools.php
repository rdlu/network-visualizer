<?php

class Controller_Tools extends Controller {

    public function action_check() {
        if(!isset($_POST['ip'])) throw new Kohana_Exception('Compulsory data not set, must be called with post',$_POST);

        $ip = $_POST['ip'];
        if(Valid::ipOrHostname($ip)) {
	         try {
		         $realip = Network::getAddress($ip);
		         $data = Snmp::instance($realip)->group('linuxManager');
               $json['address'] = $realip;
               $json['data'] = $data;
	         } catch (Network_Exception $err) {
		         if($err->getCode() == 1) {
			         $json['error'] = 'O servidor DDNS não retornou nenhum endereço IP, verifique se a sonda está corretamente registrada no DDNS..';
		         } elseif($err->getCode() == 2) {
			         $json['error'] = 'O servidor DDNS não respondeu. Verifique se o mesmo está OK.';
		         }
	         }

            $this->response->headers('Content-Type','application/json');
            if(Request::current()->is_ajax()) $this->response->body(json_encode($json));
            else throw new Kohana_Exception('This controller only accepts AJAX requests',$json);
        } else {
            throw new Kohana_Exception("Invalid format for IP Address $ip");
        }
    }

    public function action_cities() {
        if(!isset($_POST['startsWith'])) throw new Kohana_Exception('Compulsory data not set, must be called with post',$_POST);
        $post = 'Port';
        if(isset($_POST['startsWith'])) $post = (string) $_POST['startsWith'];
        $query['geonames'] = Db::select('city','state')->from('entities')->where('city','like',$post.'%')->group_by('city')->order_by('city','ASC')->execute()->as_array();
        $this->response->headers('Content-Type','application/json');
        if(Request::current()->is_ajax()) $this->response->body(json_encode($query));
        else throw new Kohana_Exception('This controller only accepts AJAX requests',$query);
    }

	public function action_entityStatus($id) {
		$id = (int) $id;
		if(Request::current()->is_ajax()) {
			$this->auto_render = false;
			$dados = Sprig::factory('entity', array("id" => $id))->load();
			$this->response->headers('Content-Type','application/json');

		}
	}

    public function action_createResultTables() {
        $this->auto_render = false;
        $this->response->headers('Content-Type','text/plain');
        $profiles = Sprig::factory('profile')->load(NULL, FALSE);
        $metrics = Sprig::factory('metric')->load(NULL, FALSE);

        foreach($profiles as $profile) {
            foreach($metrics as  $metric) {
                $model = Model_Results::factory($profile->id, $metric->id)->createDB();
                echo $metric->name."-".$profile->id." \n";
            }
        }
    }

    public function action_testSQL($process_id,$metricPlugin) {
        $this->auto_render = false;
        $this->response->headers('Content-Type','text/plain');
        $process = Sprig::factory('process', array('id' => $process_id))->load();

        if ($process->count() == 0) {
            echo "Process $process_id does not exist.\n";
        }

        $destination = $process->destination->load();
        $source = $process->source->load();
        $profile = $process->profile->load();
        $metric = Sprig::factory('metric')->load(Db::select()->where('plugin', '=', $metricPlugin));

        $toBeSQLed = array(
            'dsmax'=>lcg_value()+rand(0,10),
            'dsmin'=>lcg_value()+mt_rand(0,10),
            'dsavg'=>lcg_value()+mt_rand(0,10),
            'sdmax'=>lcg_value()+rand(0,10),
            'sdmin'=>lcg_value()+rand(0,10),
            'sdavg'=>lcg_value()+mt_rand(0,10),
            'timestamp' => date("U"),
            'stored' => date("U"),
            'process_id' => $process->id,
            'source_name' => $source->name,
            'destination_name' => $destination->name,

        );

        var_dump($toBeSQLed);
        Model_Results::factory($profile->id,$metric->id)->insert($process->id,$toBeSQLed);

    }

    public function action_hashz0r($str) {
        echo Kohana_Auth::instance()->hash("VivOGparC,.");
        echo "  ";
        echo Kohana_Auth::instance()->hash($str);
    }
}
