<?php
class CollectException extends Exception
{
}

class Controller_Collect extends Controller
{
    public function action_id()
    {
        $id = $this->request->param('destination', 0);
        $metric = $this->request->param('metric', false);
        $dsMax = $this->request->param('dsMax', null);
        $dsMin = $this->request->param('dsMin', null);
        $dsAvg = $this->request->param('dsAvg', null);
        $sdMax = $this->request->param('sdMax', null);
        $sdMin = $this->request->param('sdMin', null);
        $sdAvg = $this->request->param('sdAvg', null);
        $timestamp = $this->request->param('timestamp', null);


        $response = 'Received';
        if ($id === 0) {
            $id = $_POST['id'];
        }

        if (!$metric) {
            $metric = $_POST['metric'];
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        $authorized_collectors = Kohana::$config->load('network.collectors');
        //leve recurso de segurança
        if (!in_array($ip, $authorized_collectors)) throw new CollectException("Unrecognized collector $ip ", 1337);

        if ($id != 0) {
            $process = ORM::factory('process', $id);

            if (!$process->loaded()) {
                $response = "Process $id does not exist.";
            }
            $destination = $process->destination;
            $source = $process->source;
            $profile = $process->profile;
            $metric = ORM::factory('metric')->where('plugin', '=', $metric)->find();

            $cache = Kohana_Cache::instance('memcache')->get("$source->id-$destination->id", array());

            $toBeCached = array_merge($cache,
                array(
                    "$metric->plugin" => $sdAvg,
                    "full-$metric->plugin" => array(
                        'DSMax' => $dsMax,
                        'DSMin' => $dsMin,
                        'DSAvg' => $dsAvg,
                        'SDMax' => $sdMax,
                        'SDMin' => $sdMin,
                        'SDAvg' => $sdAvg)
                ));
            Kohana_Cache::instance('memcache')->set("$source->id-$destination->id", $toBeCached, 86400);

            //WARNING: A ordem importa
            $toBeSQLed = array(
                'process_id' => $process->id,
                'dsavg' => $dsAvg,
                'sdavg' => $sdAvg,
                'dsmin' => $dsMin,
                'sdmin' => $sdMin,
                'dsmax' => $dsMax,
                'sdmax' => $sdMax,
                'timestamp' => $timestamp,
                'source_name' => $source->name,
                'destination_name' => $destination->name,
                'stored' => date("U"),
            );

            $toBeRRDed = array(
                'LastDSMax' => $dsMax,
                'LastDSMin' => $dsMin,
                'LastDSAvg' => $dsAvg,
                'LastSDMax' => $sdMax,
                'LastSDMin' => $sdMin,
                'LastSDAvg' => $sdAvg,
            );


            Model_Results::factory($profile->id, $metric->id)->insert($process->id, $toBeSQLed);


            $roundedTimestamp = $timestamp - ($timestamp % $profile->polling);

            if (!$destination->isAndroid)
                $rrd = Rrd::instance($source->ipaddress, $destination->ipaddress)->update($metric->name, $toBeRRDed, $roundedTimestamp);
            $destination->updated = date('U');
            $source->updated = date('U');
            $process->updated = date('U');
            $destination->update();
            $source->update();
            $process->update();
            $response = "Updated S: {$source->ipaddress} D: {$destination->ipaddress}  with TS: $timestamp";

        } else {
            throw new Kohana_Exception('Invalid ID in Collect/id', $_POST);
        }

        $this->response->body($response);
    }

    public function action_kpi()
    {
        //'collect/id/<destination>/kpi/<cellID>/<brand>/<model>/<connType>/<connTech>/<signal>/<errorRate>/<numberOfIPs>/<route>/<mtu>/<dnsLatency>/<lac>',
        $id = $this->request->param('destination', 0);
        $response = 'Received';
        if ($id === 0) {
            $id = $_POST['id'];
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        $authorized_collectors = Kohana::$config->load('network.collectors');
        //leve recurso de segurança
        if (!in_array($ip, $authorized_collectors)) throw new CollectException("Unrecognized collector $ip ", 1337);

        if ($id != 0) {
            $process = ORM::factory('process', $id);

            if (!$process->loaded()) {
                $response = "Process $id does not exist.";
            }
            $destination = $process->destination;
            $source = $process->source;
            $profile = $process->profile;
            $polling = ($destination->isAndroid) ? $destination->polling : $profile->polling;

            //valores
            $values = array(
                'cell_id' => $this->request->param('cellID', null),
                'brand' => $this->request->param('brand', null),
                'model' => $this->request->param('model', null),
                'conn_type' => $this->request->param('connType', null),
                'conn_tech' => $this->request->param('connTech', null),
                'signal' => $this->request->param('signal', null),
                'error_rate' => $this->request->param('errorRate', null),
                'number_of_ips' => $this->request->param('numberOfIPs', null),
                //traceroute
                'route' => $this->request->param('route', null),
                'mtu' => $this->request->param('mtu', null),
                'dns_latency' => $this->request->param('dnsLatency', null),
                'lac' => $this->request->param('lac', null),
                'timestamp' => $this->request->param('timestamp', null),
                'polling' => $polling
            );

            $cache = Kohana_Cache::instance('memcache')->get("$source->id-$destination->id", array());

            $toBeCached = array_merge($cache,
                array("kpi" => $values));
            Kohana_Cache::instance('memcache')->set("$source->id-$destination->id", $toBeCached, 86400);

            //WARNING: A ordem importa
            $toBeSQLed = array_merge($values, array(
                'destination_name' => $destination->name,
                'source_name' => $source->name
            ));

            Model_Kpi::factory($destination->id)->insert($toBeSQLed);


            $destination->updated = date('U');
            $source->updated = date('U');
            $process->updated = date('U');
            $destination->update();
            $source->update();
            $process->update();
            $response = "KPI :: Updated S: {$source->ipaddress} D: {$destination->ipaddress}  with TS: " . $values['timestamp'];

        } else {
            throw new Kohana_Exception('Invalid ID in Collect/id', $_POST);
        }

        $this->response->body($response);

    }

    public function action_createDB()
    {
        Model_Kpi::factory(0)->createDB();
        echo "KPI Database created";
    }
}