<?php

class Rrd {

    protected $source ;

    protected $destination;

    protected static $instances = array();

    protected $groups = array();

    protected $types = array(
        0 => array('Last'),
        1 => array('Min','Max','Avg'),
    );

/**
 * @static
 * @param  string $source
 * @param  string $destination
 * @param  string $profile
 * @return Rrd
 */
	public static function instance($source,$destination) {
		if (!isset(Rrd::$instances[$source.':'.$destination])) {
            $newinstance = new Rrd();
            $newinstance->setAddress($source);
            $newinstance->setAddress($destination,false);

            Rrd::$instances[$source.':'.$destination] = $newinstance;
		}

		return Rrd::$instances[$source.':'.$destination];
	}

    public function setAddress($address,$source=true) {
        if($source) {
            if(Validate::Ip($address)) {
                $this->source = $address;
            } else throw new Kohana_Exception('Invalid SOURCE address in RRD class',$address);
        } else {
            if(Validate::Ip($address)) {
                $this->destination = $address;
            } else throw new Kohana_Exception('Invalid DESTINATION address in RRD class',$address);
        }

    }

    public function path($profileId) {
        return DATAPATH."rrd/$this->source/$this->destination/$profileId/";
    }

    public function imgPath($profileId) {
        return DOCROOT."images/rrd/$this->source/$this->destination/$profileId/";
    }

    public function filename($metric,$type,$ext='rrd') {
        return "$metric$type.$ext";
    }

    public function fullPath($profileId,$metric,$type) {
        return $this->path($profileId)."$metric$type.rrd";
    }

    /**
     * Funcao para a criacao dos arquivos RRD para um determinado perfil e metrica
     * @throws Kohana_Exception
     * @param  int $profileId
     * @param  string $metric
     * @param  int $step
     * @return Rrd
     */
    public function create($profileId,$metric,$step) {
        $heartbeat = 2*$step;
        $opts = array( "-s", "$step",
           "DS:downstream:GAUGE:$heartbeat:U:U",
           "DS:upstream:GAUGE:$heartbeat:U:U",
           "RRA:AVERAGE:0.5:1:600",
           "RRA:AVERAGE:0.5:6:700",
           "RRA:AVERAGE:0.5:24:775",
           "RRA:AVERAGE:0.5:288:797",
           "RRA:MAX:0.5:1:600",
           "RRA:MAX:0.5:6:700",
           "RRA:MAX:0.5:24:775",
           "RRA:MAX:0.5:288:797"
        );
        Fire::group('Created RRD Files: ');
        $path = $this->path($profileId);
        if(!is_dir($path)) {
            Fire::info('Creating Directory '.$path);
            mkdir($path,0774,true);
        }
        foreach($this->types[0] as $l1)
            foreach($this->types[1] as $l2) {
                $filename = $this->filename($metric,$l1.$l2);
                Fire::info($filename);
                $ret = rrd_create($path.$filename, $opts, count($opts));

                if($ret == 0) {
                    Fire::error($opts,'RRD File Create Error: '.rrd_error());
                    Kohana_Log::instance()->add('error',"RRD File Create Error: $path.$filename",$opts);
                } else {
                    Kohana_Log::instance()->add('info',"RRD File Created $path$filename with $step second step");
                }
            }
        Fire::groupEnd();
        return $this;
    }

    /**
     * Funcao que atualiza os dados de um arquivo RRD
     * @param  int $profileId
     * @param  string $metric
     * @param  array $data
     * @return Rrd
     */
    public function update($profileId,$metric,array $data) {
        Fire::group("Updating RRD Files - S:$this->source D:$this->destination P:$profileId");
        $path = $this->path($profileId);
        foreach($this->types[0] as $l1)
            foreach($this->types[1] as $l2) {
                $filename = $this->filename($metric,$l1.$l2);
                $downstream = $data[$l1.'DS'.$l2];
                $upstream = $data[$l1.'SD'.$l2];
                Fire::info($filename.' : '.$downstream.' : '.$upstream);
                $ret = rrd_update($path.$filename,"N:$downstream:$upstream");

                if($ret == 0) {
                    Kohana_Log::instance()->add('warning','RRD Update Failed',array($path.$filename,$downstream,$upstream));
                    Fire::error(array($path.$filename,$downstream,$upstream),'RRD Update Failed: '.rrd_error());
                }
            }
        Fire::groupEnd();
        return $this;
    }

    public function graph($profileId,$metric,$start = null,$end = null,$measure=false) {
        $rrdPath = $this->path($profileId);
        $path = $this->imgPath($profileId);

        Fire::group('Creating Graph from RRD from '.$this->source.' to '.$this->destination);
        if(!is_dir($path)) {
            Fire::info('Creating Directory '.$path);
            mkdir($path,0774,true);
        }
        $measures = Kohana::config("measure.$metric");

        if(!$start) $start = date('d.m.Y H:i',mktime(date('H'), date('i'), date('s'), date("m") , date("d") - 1, date("Y")));
        if(!$end) $end = date('d.m.Y H:i');

        Fire::info("Fetched range from $start to $end");

        if($measure) {

        } else {
            $choosenMeasure = $measures['view'];
            $choosenFactor = 1024;
        }
        $verboseMeasure = __($choosenMeasure);

        foreach($this->types[0] as $l1)
            foreach($this->types[1] as $l2) {
                /**
                 * Opcoes da geracao do RRD
                 */
                $rrdfn = $filename = $this->filename($metric,$l1.$l2);
                $title = "<b>".ucfirst($metric)." ".__($l2)."</b> <small>($start até $end)</small>\r";
                 $opts = array( "-s $start", "-e $end","-t $title ","-P", "-v $verboseMeasure",
                     "-w 800","-h 200",
                 "DEF:ds=$rrdPath$filename:downstream:AVERAGE",
                 "DEF:sd=$rrdPath$filename:upstream:AVERAGE",
                 "LINE2:ds#990000:Downstream",
                 "LINE2:sd#000099:Upstream\\r",
                 "CDEF:dsm=ds,$choosenFactor,*",
                 "CDEF:sdm=sd,$choosenFactor,*",
                 "COMMENT:\\n",
                 "GPRINT:ds:AVERAGE:Pto Méd Down\: %6.2lf %S$choosenMeasure",
                 "COMMENT:  ",
                 "GPRINT:ds:MAX:Pto Máx Down\: %6.2lf %S$choosenMeasure\\r",
                 "GPRINT:sd:AVERAGE:Pto Méd Up\:   %6.2lf %S$choosenMeasure",
                 "COMMENT: ",
                 "GPRINT:sd:MAX: Pto Máx Up\:   %6.2lf %S$choosenMeasure\\r"
               );


                $filename = $this->filename($metric,$l1.$l2,'png');
                Fire::info($filename);
                $ret = rrd_graph($path.$filename, $opts, count($opts));

                if(!is_array($ret)) {
                    Fire::error($opts,'RRD Graph File Create Error: '.rrd_error());
                    Kohana_Log::instance()->add('error',"RRD Graph Create Error: $path$filename");
                }
            }
        Fire::groupEnd();
    }
}
