<?php
class CollectException extends Exception
{
}

class Controller_Collect extends Controller
{
    public function action_id()
    {
        $id = $this->request->param('id', 0);
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
            $process = Sprig::factory('process', array('id' => $id))->load();

            if ($process->count() == 0) {
                $response = "Process $id does not exist.";
            }
            $destination = $process->destination->load();
            $source = $process->source->load();
            $profile = $process->profile->load();
            $metric = Sprig::factory('metric')->load(Db::select()->where('plugin', '=', $metric));

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


            $roundedTimestamp = $timestamp - ($timestamp % $destination->polling);

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
}
