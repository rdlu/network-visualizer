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
	 * @var bool
	 */
	public $errors = false;

/**
 * @static
 * @param  string $source IP Address
 * @param  string $destination IP Address
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

	public function imgSrc($profileId,$metric,$type) {
		return "images/rrd/$this->source/$this->destination/$profileId/".$this->filename($metric,$type,'png');
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
        $opts[] = "-s";
        $opts[] = "$step";
        $opts[] = "DS:downstream:GAUGE:$heartbeat:U:U";
        if($metric != 'rtt')
	        $opts[] = "DS:upstream:GAUGE:$heartbeat:U:U";
        $opts[] = "RRA:AVERAGE:0.5:1:600";
        $opts[] = "RRA:AVERAGE:0.5:6:700";
        $opts[] = "RRA:AVERAGE:0.5:24:775";
        $opts[] = "RRA:AVERAGE:0.5:288:797";
        $opts[] = "RRA:MAX:0.5:1:600";
        $opts[] = "RRA:MAX:0.5:6:700";
        $opts[] = "RRA:MAX:0.5:24:775";
        $opts[] = "RRA:MAX:0.5:288:797";
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
                    $this->errors = true;
                } else {
                    Kohana_Log::instance()->add('INFO',"RRD File Created $path$filename with $step second step");
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
    public function update($profileId,$metric,array $data,$timestamp = 'N') {
	    $ts = date("d.m.Y H:i:s T",$timestamp);
        Fire::group("Updating RRD Files - S:$this->source D:$this->destination P:$profileId TS:$ts");
        $path = $this->path($profileId);
        foreach($this->types[0] as $l1)
            foreach($this->types[1] as $l2) {
                $filename = $this->filename($metric,$l1.$l2);
                $downstream = $data[$l1.'DS'.$l2];

                if($metric != 'rtt') {
	                $upstream = $data[$l1.'SD'.$l2];
                   //Fire::info("$filename TIME $timestamp : DS $downstream : SD $upstream");
                   $numbers = "DS $downstream : SD $upstream";
                   $ret = rrd_update($path.$filename,"$timestamp:$downstream:$upstream");
                } else {
	                //Fire::info("$filename TIME $timestamp : RTT $downstream");
                   $ret = rrd_update($path.$filename,"$timestamp:$downstream");
                   $numbers = "DS $downstream";
                }

                Fire::info("RRD Update : TIME $timestamp : $numbers on $path$filename");

                if($ret == 0) {
	                $erf = rrd_error();
                    Kohana_Log::instance()->add("WARN","RRD Update Failed :: $filename TIME $timestamp : $numbers :: ".$erf);
                    Fire::error(array($path.$filename,$numbers),'RRD Update Failed: '.$erf);
                }
            }
        Fire::groupEnd();
        return $this;
    }

   /**
    * @param int $profileId
    * @param string $metric
    * @param string $start
    * @param string $end
    * @param bool $measure
    * @return void
    */
	public function graph($profileId,$metric,$start = null,$end = null,$measure=false) {
        $rrdPath = $this->path($profileId);
        $path = $this->imgPath($profileId);

        Fire::group("Creating RRD $metric graph from $this->source to $this->destination, profile $profileId",array('Collapsed'=>"true"));
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

                if($metric != 'rtt')
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
                else
	                $opts = array( "-s $start", "-e $end","-t $title ","-P", "-v $verboseMeasure",
		                "-w 800","-h 200",
		                "DEF:ds=$rrdPath$filename:downstream:AVERAGE",
		                "LINE2:ds#990000:Roundtrip Time",
		                "CDEF:dsm=ds,$choosenFactor,*",
		                "COMMENT:\\n",
		                "GPRINT:ds:AVERAGE:Pto Méd RTT\: %6.2lf %S$choosenMeasure",
		                "COMMENT:  ",
		                "GPRINT:ds:MAX:Pto Máx RTT\: %6.2lf %S$choosenMeasure\\r"
	                );


                $filename = $this->filename($metric,$l1.$l2,'png');
                $imgs[] = $this->imgSrc($profileId,$metric,$l1.$l2);
                Fire::info($this->imgSrc($profileId,$metric,$l1.$l2));
                $ret = rrd_graph($path.$filename, $opts, count($opts));

                if(!is_array($ret)) {
                    Fire::error($opts,'RRD Graph File Create Error: '.rrd_error());
                    Kohana_Log::instance()->add('ERROR',"RRD Graph Create Error: $path$filename");
                }
            }
        Fire::groupEnd();
	     return $imgs;
    }

	/**
	 * Funcao que converte uma data comum (dd/mm/yyyy) para o formato militar yyyymmdd
	 * @static
	 * @param  $str
	 * @return bool|string
	 */
	public static function converteData($str) {
		if (preg_match("/^(0?[1-9]|[12][0-9]|3[01])[\/\.\- ](0?[1-9]|1[0-2])[\/\.\- ](19|20\d{2})$/", $str, $matches))
			return $matches[3].$matches[2].$matches[1];
		return false;
	}
}
