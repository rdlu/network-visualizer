<?php
/**
 *
 * @author Rodrigo Dlugokenski
 */
class Controller_Reports extends Controller_Skeleton
{

    public $auth_required = array('login');

    // Controls access for separate actions
    // 'adminpanel' => 'admin' will only allow users with the role admin to access action_adminpanel
    // 'moderatorpanel' => array('login', 'moderator') will only allow users with the roles login and moderator to access action_moderatorpanel
    public $secure_actions = FALSE;

    public function before()
    {
        if ($this->auto_render) {
            parent::before();
            $this->template->title .= "Relatórios :: ";

            $scripts = array(
                'js/flot/jquery.flot.min.js',
                'js/flot/jquery.flot.crosshair.js',
                'js/flot/jquery.flot.selection.js',
                'js/flot/jquery.flot.resize.js',
                'js/dev/conversion.js',
                'js/dev/utils.js'
            );

            $this->template->scripts = array_merge($scripts, $this->template->scripts);
        }
    }

    public function action_Index()
    {
        $view = View::factory('reports/index');

        $entities = ORM::factory('entity')->find_all();
        $view->bind('entities', $entities)
            ->set('defaultManager', Sonda::getDefaultManager());
        $this->template->content = $view;
    }

    public function action_View()
    {
        $sId = (int)$this->request->param('source_id', 0);
        $dId = (int)$this->request->param('destination_id', 0);
        $start = $this->request->param('start', 0);
        $end = $this->request->param('end', 0);
        $stime = $this->request->param('stime', 0);
        $etime = $this->request->param('etime', 0);

        $view = View::factory('reports/view');
        if (Request::current()->is_ajax()) {
            $this->auto_render = false;
            $sId = (int)$_POST['source'];
            $dId = (int)$_POST['destination'];

            $relative = (isset($_POST['relative'])) ? $_POST['relative'] : false;

            if ($relative) {
                $inicio = strtotime($relative);
                $end = Date("U");
            } else {
                $start = $_POST['startDate'];
                $end = $_POST['endDate'];
                $stime = $_POST['startHour'];
                $etime = $_POST['endHour'];
                $inicio = Rrd::converteData($start) . " " . $stime;
                $fim = Rrd::converteData($end) . " " . $etime;
            }

        }

        //Valida dados
        if (!Valid::data($start)) throw new Kohana_Exception("Start Date not valid", array($start));
        if (!Valid::data($end)) throw new Kohana_Exception("End Date not valid", array($end));
        if (!Valid::hora($stime)) throw new Kohana_Exception("Start time not valid", array($stime));
        if (!Valid::hora($etime)) throw new Kohana_Exception("End time not valid", array($etime));

        $processes = ORM::factory('process')->where('destination_id', '=', $dId)
            ->where('source_id', '=', $sId)->find_all();
        $source = ORM::factory('entity', $sId);
        $destination = ORM::factory('entity', $dId);

        if ($destination->isAndroid) die('<span class="nice big error" style="padding-left: 30px !important;">Agentes android não possuem gráficos RRD</span>');

        //Gerando os graficos
        $rrd = Rrd::instance($source->ipaddress, $destination->ipaddress);

        $count = $processes->count();

        //Fire::group("Report status for $source->ipaddress to $destination->ipaddress",array('Collapsed'=>'true'))->info("Number of processes: $count");

        if ($count) {

            $metrics2 = array();

            foreach ($processes as $process) {
                //Fire::info($process->as_array(),"Process 1, ID: $process->id");
                $profile = $process->profile;
                $metrics = $process->metrics->as_array('order');
                ksort($metrics);
                //Fire::error($metrics);
                foreach ($metrics as $metric) {
                    $img[$metric->name] = $rrd->graph($metric->name, $inicio, $fim);
                }
            }

            //Fire::group("Images path")->info($img)->groupEnd();
            //Fire::groupEnd();

            if (Request::current()->is_ajax()) {
                $view->bind('images', $img)->bind('metrics', $metrics)
                    ->bind('processes', $processes);
                $this->response->headers('Cache-Control', "no-cache");
                $this->response->body($view);
            } else {
                $this->template->content = $view;
            }


        } else {
            $this->template->content = "Não há nenhum processo de medição entre $source->name ($source->ipaddress) e $destination->name ($destination->ipaddress)";
        }
    }

    public function action_ViewFlot()
    {
        $sId = (int)$this->request->param('source_id', 0);
        $dId = (int)$this->request->param('destination_id', 0);
        $start = $this->request->param('start', 0);
        $end = $this->request->param('end', 0);
        $stime = $this->request->param('stime', 0);
        $etime = $this->request->param('etime', 0);

        $view = View::factory('reports/viewFlot');
        if (Request::current()->is_ajax()) {
            $this->auto_render = false;
            $sId = (int)$_POST['source'];
            $dId = (int)$_POST['destination'];

            $relative = (isset($_POST['relative'])) ? $_POST['relative'] : false;

            if ($relative) {
                $inicio = strtotime($relative);
                $end = Date("U");
            } else {
                $start = $_POST['startDate'];
                $end = $_POST['endDate'];
                $stime = $_POST['startHour'];
                $etime = $_POST['endHour'];
                $inicio = Rrd::converteData($start) . " " . $stime;
                $fim = Rrd::converteData($end) . " " . $etime;
            }

        }

        //Valida dados
        if (!Valid::data($start)) throw new Kohana_Exception("Start Date not valid", array($start));
        if (!Valid::data($end)) throw new Kohana_Exception("End Date not valid", array($end));
        if (!Valid::hora($stime)) throw new Kohana_Exception("Start time not valid", array($stime));
        if (!Valid::hora($etime)) throw new Kohana_Exception("End time not valid", array($etime));

        $processes = ORM::factory('process')->where('destination_id', '=', $dId)
            ->where('source_id', '=', $sId)->find_all();
        $source = ORM::factory('entity', $sId);
        $destination = ORM::factory('entity', $dId);

        if ($destination->isAndroid) die('<span class="nice big error" style="padding-left: 30px !important;">Agentes android não possuem gráficos RRD</span>');

        $count = $processes->count();

        if ($count) {

            foreach ($processes as $process) {
                $profile = $process->profile;
                $metrics = $process->metrics->as_array('order');
                ksort($metrics);
                //Fire::error($metrics);
                foreach ($metrics as $metric) {
                    $flot[$metric->name] = $this->singleFlot($source->id, $destination->id, $metric->name, $inicio, $fim, 1000, true);
                }
            }

            if (Request::current()->is_ajax()) {
                $view->set('results', Zend_Json::encode($flot))
                    ->bind('startDate', $start)
                    ->bind('endDate', $end)
                    ->bind('startHour', $stime)
                    ->bind('endHour', $etime)
                    ->bind('metrics', $metrics)
                    ->bind('processes', $processes)
                    ->set('source', $source->as_array())
                    ->set('destination', $destination->as_array());
                $this->response->headers('Cache-Control', "no-cache");
                $this->response->body($view);
            } else {
                $this->template->content = $view;
            }


        } else {
            $this->template->content = "Não há nenhum processo de medição entre $source->name ($source->ipaddress) e $destination->name ($destination->ipaddress)";
        }
    }

    public function action_viewXport()
    {
        $sId = (int)$this->request->param('source_id', 0);
        $dId = (int)$this->request->param('destination_id', 0);

        $view = View::factory('reports/viewXport');
        if (Request::current()->is_ajax()) {
            $this->auto_render = false;
            $sId = (int)$_POST['source'];
            $dId = (int)$_POST['destination'];
        }

        $processes = ORM::factory('process')->where('destination_id', '=', $dId)
            ->where('source_id', '=', $sId)->find_all();
        $source = ORM::factory('entity', $sId);
        $destination = ORM::factory('entity', $dId);

        $count = $processes->count();

        if ($count) {

            foreach ($processes as $process) {
                $profile = $process->profile;
                $metrics = $process->metrics->as_array('order');
                ksort($metrics);
            }

            if (Request::current()->is_ajax()) {
                $view->bind('metrics', $metrics)
                    ->bind('processes', $processes)
                    ->set('source', $source->as_array())
                    ->set('destination', $destination->as_array());
                $this->response->headers('Cache-Control', "no-cache");
                $this->response->body($view);
            } else {
                $this->template->content = $view;
            }

        } else {
            $this->template->content = "Não há nenhum processo de medição entre $source->name ($source->ipaddress) e $destination->name ($destination->ipaddress)";
        }

    }

    public function action_xport()
    {
        $this->auto_render = false;
        $startDate = $_POST['startDate'];
        $startHour = $_POST['startHour'];
        $endDate = $_POST['endDate'];
        $endHour = $_POST['endHour'];
        $metricId = $_POST['metric'];
        $sourceId = (int)$_POST['source'];
        $destinationId = (int)$_POST['destination'];

        $source = ORM::factory('entity', $sourceId);
        $destination = ORM::factory('entity', $destinationId);
        $metric = ORM::factory('metric', $metricId);
        $profile = $metric->profile;
        $process = ORM::factory('process')
            ->where('destination_id', '=', $destinationId)
            ->where('source_id', '=', $sourceId)
            ->where('profile_id', '=', $profile->id)->find();

        $filename = "momreport-" . $destination->name . "-" . $metric->name . "-" . $startDate . $startHour . "-" . $endDate . $endHour;

        list($day, $month, $year) = explode('/', $startDate);
        list($hour, $minutes) = explode(':', $startHour);
        $startTimestamp = mktime($hour, $minutes, 0, $month, $day, $year);
        $startSQLTimestamp = $year . "-" . $month . "-" . $day . " " . $startHour;

        list($day, $month, $year) = explode('/', $endDate);
        list($hour, $minutes) = explode(':', $endHour);
        $endTimestamp = mktime($hour, $minutes, 0, $month, $day, $year);
        $endSQLTimestamp = $year . "-" . $month . "-" . $day . " " . $endHour;

        $results = Model_Results::factory($profile->id, $metric->id)->query($process->id, $startSQLTimestamp, $endSQLTimestamp);

        $separator = ";";
        $values = array(
            "timestamp" => "Horario",
            "dsavg" => "Up (Avg)",
            "sdavg" => "Down (Avg)",
            "dsmin" => "Up (Min)",
            "sdmin" => "Down (Min)",
            "dsmax" => "Up (Max)",
            "sdmax" => "Down (Max)"
        );
        $header = implode($separator, $values);
        $body = "";

        foreach ($results as $result) {
            $line = "";
            foreach ($values as $column => $name) {
                $line .= str_replace(".", ",", $result[$column]) . $separator;
            }
            $body .= $line . "\r\n";
        }

        $this->response->headers("Content-Disposition", "attachment;filename=$filename.csv");
        $this->response->headers('Content-Type', 'text/csv');

        $this->response->body($header . "\r\n" . $body);
    }

    public function action_viewSql()
    {
        $sId = (int)$this->request->param('source_id', 0);
        $dId = (int)$this->request->param('destination_id', 0);

        $view = View::factory('reports/viewSql');
        if (Request::current()->is_ajax()) {
            $this->auto_render = false;
            $sId = (int)$_POST['source'];
            $dId = (int)$_POST['destination'];

            $relative = (isset($_POST['relative'])) ? $_POST['relative'] : false;

            if ($relative) {
                $inicio = strtotime($relative);
                $end = Date("U");
            } else {
                $start = $_POST['startDate'];
                $end = $_POST['endDate'];
                $stime = $_POST['startHour'];
                $etime = $_POST['endHour'];
                $inicio = Date::toTimestamp($start . " " . $stime, '%d/%m/%Y %H:%M');
                $fim = Date::toTimestamp($end . " " . $etime, '%d/%m/%Y %H:%M');
            }

        }

        $processes = ORM::factory('process')->where('destination_id', '=', $dId)
            ->where('source_id', '=', $sId)->find_all();
        $source = ORM::factory('entity', $sId);
        $destination = ORM::factory('entity', $dId);

        $count = $processes->count();

        if ($count) {

            foreach ($processes as $process) {
                $profile = $process->profile;
                $metrics = $process->metrics->as_array('order');
                ksort($metrics);
                //Fire::error($metrics);
                foreach ($metrics as $metric) {
                    $flot[$metric->name] = $this->singleSQLFlot($source->id, $destination->id, $metric->id, $inicio, $fim, 1000, true);
                }
            }

            if (Request::current()->is_ajax()) {
                $view->set('results', Zend_Json::encode($flot))
                    ->bind('startDate', $start)
                    ->bind('endDate', $end)
                    ->bind('startHour', $stime)
                    ->bind('endHour', $etime)
                    ->bind('inicio', $inicio)
                    ->bind('fim', $fim)
                    ->bind('metrics', $metrics)
                    ->bind('processes', $processes)
                    ->set('source', $source->as_array())
                    ->set('destination', $destination->as_array());
                $this->response->headers('Cache-Control', "no-cache");
                $this->response->body($view);
            } else {
                $this->template->content = $view;
            }


        } else {
            $this->template->content = "Não há nenhum processo de medição entre $source->name ($source->ipaddress) e $destination->name ($destination->ipaddress)";
        }
    }

    public function action_xml()
    {
        $pId = (int)$this->request->param('source_id', 0);
        $metric = $this->request->param('metric');
        //ex 25/01/2011
        $start = $this->request->param('start', 0);
        $end = $this->request->param('end', 0);
        //ex 13:00
        $stime = $this->request->param('stime', 0);
        $etime = $this->request->param('etime', 0);

        if (Request::current()->is_ajax()) {
            $this->auto_render = false;
            /*$pId = (int) $_POST['processId'];
               $start = $_POST['startDate'];
              $end = $_POST['endDate'];
              $stime = $_POST['startHour'];
              $etime = $_POST['endHour'];
              $metric = $_POST['metric'];*/

            //Valida dados
            if (!Valid::data($start)) throw new Kohana_Exception("Start Date not valid", array($start));
            if (!Valid::data($end)) throw new Kohana_Exception("End Date not valid", array($end));
            if (!Valid::hora($stime)) throw new Kohana_Exception("Start time not valid", array($stime));
            if (!Valid::hora($etime)) throw new Kohana_Exception("End time not valid", array($etime));

            $process = ORM::factory('process', $pId);

            if ($process->loaded()) {
                $source = $process->source;
                $destination = $process->destination;
                $profile = $process->profile;
                $rrd = Rrd::instance($source->ipaddress, $destination->ipaddress);
                $inicio = Rrd::converteData($start) . " " . $stime;
                $fim = Rrd::converteData($end) . " " . $etime;
                $xml = $rrd->xml($profile->id, $metric, $inicio, $fim);
                $this->response->body(Zend_Json::fromXml($xml));

            } else {
                throw new Kohana_Exception("Processo $pId nao encontrado");
            }

        } else throw new Kohana_Exception("Essa ação somente responde requisições AJAX");
    }

    public function action_View2()
    {
        $sId = (int)$this->request->param('source_id', 0);
        $dId = (int)$this->request->param('destination_id', 0);
        $start = $this->request->param('start', 0);
        $end = $this->request->param('end', 0);
        $stime = $this->request->param('stime', 0);
        $etime = $this->request->param('etime', 0);

        $view = View::factory('reports/view');
        if (Request::current()->is_ajax()) {
            $this->auto_render = false;
            $sId = (int)$_POST['source'];
            $dId = (int)$_POST['destination'];
            $start = $_POST['startDate'];
            $end = $_POST['endDate'];
            $stime = $_POST['startHour'];
            $etime = $_POST['endHour'];
        }

        //Valida dados
        if (!Valid::data($start)) throw new Kohana_Exception("Start Date not valid", array($start));
        if (!Valid::data($end)) throw new Kohana_Exception("End Date not valid", array($end));
        if (!Valid::hora($stime)) throw new Kohana_Exception("Start time not valid", array($stime));
        if (!Valid::hora($etime)) throw new Kohana_Exception("End time not valid", array($etime));

        $processes = ORM::factory('process')->where('destination_id', '=', $dId)
            ->where('source_id', '=', $sId)->find_all();
        $source = ORM::factory('entity', $sId);
        $destination = ORM::factory('entity', $dId);

        //Gerando os graficos
        $rrd = Rrd::instance($source->ipaddress, $destination->ipaddress);

        $count = $processes->count();

        //Fire::group("Report status for $source->ipaddress to $destination->ipaddress",array('Collapsed'=>'true'))->info("Number of processes: $count");

        if ($count) {
            $inicio = Rrd::converteData($start) . " " . $stime;
            $fim = Rrd::converteData($end) . " " . $etime;

            foreach ($processes as $process) {
                //Fire::info($process->as_array(),"Process 1, ID: $process->id");
                $profile = $process->profile;
                $metrics = $profile->metrics;
                foreach ($metrics as $metric) {
                    $img[$profile->id][$metric->name] = $rrd->graph($profile->id, $metric->name, $inicio, $fim);
                }
            }

            //Fire::group("Images path")->info($img)->groupEnd();
            //Fire::groupEnd();

            if (Request::current()->is_ajax()) {
                $view->bind('images', $img)
                    ->bind('processes', $processes);
                $this->response->body($view);
            } else {
                $this->template->content = $view;
            }


        } else {
            $this->template->content = "Não há nenhum processo de medição entre $source->name ($source->ipaddress) e $destination->name ($destination->ipaddress)";
        }
    }

    public function action_json()
    {
        $this->auto_render = false;

        /*$source = $_POST['source'];
          $destination = $_POST['destination'];
          $metric = $_POST['metric'];*/
        $source = 5;
        $destination = 8;
        $metric = 'capacity';

        $pair = Pair::instance($source, $destination);

//		$this->response->headers('Content-Type','application/json');
        $this->response->body(Zend_Json::encode($pair->getResult($metric)));
    }

    protected function singleCSV($source, $destination, $metric, $start = false, $end = false, $multiplier = 1, $timeOffset = false)
    {
        $start = ($start) ? $start : date("U", time() - 3600);
        $end = ($end) ? $end : date("U");
        $pair = Pair::instance($source, $destination);
        $c = ";";

        //$types = array('Avg','Min','Max');
        $type = 'Avg';
        $line = "\r\n";
        $exportString = $metric . $c . $source . $c . $destination . $c . $start . $c . $end . $line;

        //foreach($types as $type) {
        $json = $pair->getRrdInstance()->json($metric, $start, $end, $type);
        $obj = Zend_Json::decode($json, Zend_Json::TYPE_OBJECT)->xport;


        if (is_array($obj->meta->legend->entry)) {
            foreach ($obj->meta->legend->entry as $x => $z) {
                $entries[0][$x] = $z;
            }
        } else {
            $results->labels = array($obj->meta->legend->entry);
            $rrdlabel = explode(" ", $obj->meta->legend->entry);
            $direction[0] = $rrdlabel[1];
            $values[$direction[0]] = null;
            $results->values = $values;
        }

        $now = new DateTime();
        $offset = $now->getOffset();
        foreach ($obj->data->row as $k => $row) {
            if (is_array($row->v)) {
                foreach ($row->v as $j => $value) {
                    $value = ($value == 'NaN') ? null : $value;
                    $time = ($timeOffset) ? ($row->t + $offset) : ($row->t);
                    $values[$direction[$j]][$time * $multiplier] = $value;
                }
                $results->values = $values;
            } else {
                $value = ($row->v == 'NaN') ? null : $row->v;
                $time = ($timeOffset) ? ($row->t + $offset) : ($row->t);
                $values[$direction[0]][$time * $multiplier] = $value;
                $results->values = $values;
            }
        }

        $resultTypes->$type = $results;
        //}
    }

    protected function singleSQLFlot($source, $destination, $metricId, $start = false, $end = false, $multiplier = 1, $timeOffset = false)
    {
        $start = ($start) ? $start : date("U", time() - 3600);
        $end = ($end) ? $end : date("U");
        $pair = Pair::instance($source, $destination);

        $types = array('Avg', 'Min', 'Max');

        $resultTypes = new stdClass();

        $metric = ORM::factory('metric', $metricId);
        $profile = $metric->profile;
        $process = ORM::factory('process')
            ->where('destination_id', '=', $pair->getDestination()->id)
            ->where('source_id', '=', $pair->getSource()->id)
            ->where('profile_id', '=', $profile->id)
            ->find();

        $startSQLTimestamp = date("Y-m-d H:i:s", $start);
        $endSQLTimestamp = date("Y-m-d H:i:s", $end);
        $results = Model_Results::factory($profile->id, $metric->id)->query($process->id, $startSQLTimestamp, $endSQLTimestamp);

        $return = new stdClass();

        foreach ($types as $type) {
            $return->$type = new stdClass();
            $return->$type->labels = array("$metric ds", "$metric sd");
            $return->$type->values = new stdClass();
            $return->$type->values->ds = new stdClass();
            $return->$type->values->sd = new stdClass();
        }

        foreach ($results as $line) {
            foreach ($types as $type) {
                $type2 = strtolower($type);
                $ts = Date::sqlTimestamp2Unix($line['timestamp'], true) * 1000;
                if ($metric->plugin == 'rtt') {
                    $return->$type->values->ds->$ts = $line['ds' . $type2];
                    $return->$type->values->sd->$ts = $line['sd' . $type2];
                } else {
                    $return->$type->values->ds->$ts = $line['sd' . $type2];
                    $return->$type->values->sd->$ts = $line['ds' . $type2];
                }
            }

        }

        return $return;
    }

    protected function multiSQLFlot($source, $destination)
    {
        $pair = Pair::instance($source, $destination);
        $metrics = $pair->getMetrics();

        $results = array();

        foreach ($metrics as $k => $metric) {
            $results[$metric->name] = $this->singleSQLFlot($source, $destination, $metric->name);
        }

        return $results;
    }

    /**
     * @param int $source
     * @param int $destination
     * @param $metric
     * @param bool $start
     * @param bool $end
     * @param int $multiplier
     * @param bool $timeOffset
     * @return stdClass
     */
    protected function singleFlot($source, $destination, $metric, $start = false, $end = false, $multiplier = 1, $timeOffset = false)
    {
        $start = ($start) ? $start : date("U", time() - 3600);
        $end = ($end) ? $end : date("U");
        $pair = Pair::instance($source, $destination);

        $types = array('Avg', 'Min', 'Max');

        $resultTypes = new stdClass();

        foreach ($types as $type) {
            $json = $pair->getRrdInstance()->json($metric, $start, $end, $type);
            $obj = Zend_Json::decode($json, Zend_Json::TYPE_OBJECT)->xport;

            $results = new stdClass();

            $results->values = new stdClass();

            $zero = 0;

            if (is_array($obj->meta->legend->entry)) {
                $results->labels = $obj->meta->legend->entry;
                foreach ($obj->meta->legend->entry as $x => $z) {
                    $rrdlabel = explode(" ", $z);
                    $direction[$x] = $rrdlabel[1];
                    $values[$direction[$x]] = null;
                }
                $results->values = $values;
            } else {
                $results->labels = array($obj->meta->legend->entry);
                $rrdlabel = explode(" ", $obj->meta->legend->entry);
                $direction[0] = $rrdlabel[1];
                $values[$direction[0]] = null;
                $results->values = $values;
            }

            $now = new DateTime();
            $offset = $now->getOffset();
            foreach ($obj->data->row as $k => $row) {
                if (is_array($row->v)) {
                    foreach ($row->v as $j => $value) {
                        $value = ($value == 'NaN') ? null : $value;
                        $time = ($timeOffset) ? ($row->t + $offset) : ($row->t);
                        $values[$direction[$j]][$time * $multiplier] = $value;
                    }
                    $results->values = $values;
                } else {
                    $value = ($row->v == 'NaN') ? null : $row->v;
                    $time = ($timeOffset) ? ($row->t + $offset) : ($row->t);
                    $values[$direction[0]][$time * $multiplier] = $value;
                    $results->values = $values;
                }
            }

            $resultTypes->$type = $results;
        }

        //$json = $pair->getRrdInstance()->json($metric,$start,$end);

        return $resultTypes;
    }

    protected function multiFlot($source, $destination)
    {
        $pair = Pair::instance($source, $destination);
        $metrics = $pair->getMetrics();

        $results = array();

        foreach ($metrics as $k => $metric) {
            $results[$metric->name] = $this->singleFlot($source, $destination, $metric->name);
        }

        return $results;
    }

    public function action_flot()
    {
        $source = (int)$this->request->param('source');
        $destination = (int)$this->request->param('destination', false);
        $metric = $this->request->param('metric', false);

        $this->auto_render = false;

        if ($metric) {
            $results[$metric] = $this->singleFlot($source, $destination, $metric);
        } else {
            $results = $this->multiFlot($source, $destination);
        }

        $this->response->headers('Content-Type', 'application/json');
        $this->response->body(Zend_Json::encode($results));
    }

    public function action_lastResultsFromPair()
    {
        $source = (int)$this->request->param('source');
        $destination = (int)$this->request->param('destination');

        $this->auto_render = false;

        $pair = Pair::instance($source, $destination);

        if (true || Request::current()->is_ajax()) $this->response->headers('Content-Type', 'application/json');
        $this->response->body(Zend_Json::encode($pair->lastResults()));
    }

    //Rodrigo, se possível, altera essa função sempre em synthesizing - action_destsondas. Vlw
    public function action_lastResultsFromSource()
    {
        $source = (int)$this->request->param('source');

        $this->auto_render = false;

        //$source = $_POST['source'];

        $source = ORM::factory('entity', $source);
        $processes = ORM::factory('process')->group_by('destination_id')->where('source_id', '=', $source->id)->find_all();
        $resp = array();
        foreach ($processes as $process) {
            $resp[] = $process->destination;
        }

        foreach ($resp as $destination) {
            $pair = Pair::instance($source->id, $destination->id);
            $resultss[] = $pair->lastResults();
        }

        if (Request::current()->is_ajax()) $this->response->headers('Content-Type', 'application/json');
        $this->response->body(Zend_Json::encode($resultss));
    }
}
