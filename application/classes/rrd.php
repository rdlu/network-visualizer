<?php

class Rrd {

	protected $source;

	protected $destination;

	protected static $instances = array();

	protected $groups = array();

	protected $types = array(
		0 => array('Last'),
		1 => array('Avg', 'Max', 'Min'),
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
	public static function instance($source, $destination) {
		if (!isset(Rrd::$instances[$source . ':' . $destination])) {
			$newinstance = new Rrd();
			$newinstance->setSource($source);
			$newinstance->setDestination($destination);

			Rrd::$instances[$source . ':' . $destination] = $newinstance;
		}

		return Rrd::$instances[$source . ':' . $destination];
	}

	public function setDestination($address) {
		if (Valid::ipOrHostname($address)) {
				$this->destination = $address;
		} else throw new Kohana_Exception("Invalid DESTINATION address in RRD class $address");
	}

	public function setSource($address) {
			if (Valid::ipOrHostname($address)) {
				$this->source = $address;
			} else throw new Kohana_Exception("Invalid SOURCE address in RRD class $address");
	}

	public function getSource() {
		//if (Valid::ip($this->source))
			return $this->source;
		//else return Network::getAddress($this->source);
	}

	public function getDestination() {
		//if (Valid::ip($this->destination))
			return $this->destination;
		//else return Network::getAddress($this->destination);
	}

	public function path() {
		$source = $this->getSource();
		$destination = $this->getDestination();
		return DATAPATH . "rrd/$source/$destination/";
	}

	public function imgPath() {
		$source = $this->getSource();
		$destination = $this->getDestination();
		return DOCROOT . "images/rrd/$source/$destination/";
	}

	public function imgSrc($metric, $type) {
		$source = $this->getSource();
		$destination = $this->getDestination();
		return "images/rrd/$source/$destination/" . $this->filename($metric, $type, 'png');
	}

	public function filename($metric, $type, $ext = 'rrd') {
		return "$metric$type.$ext";
	}

	public function fullPath($metric, $type) {
		return $this->path() . "$metric$type.rrd";
	}

	/**
	 * Funcao para a criacao dos arquivos RRD para um determinado perfil e metrica
	 * @throws Kohana_Exception
	 * @param  string $metric
	 * @param  int $step
	 * @return Rrd
	 */
	public function create($metric, $step) {
		$heartbeat = 2 * $step;
		$mainPrecision = 1209600 / $step; //1 semana
		$secondaryPrecision = 2628000 / $step; //1 mes
		$thirdPrecision = 15768000 / $step; //6 meses
		$fourthPrecision = 31536000 / $step; //1 anos
		$fifthPrecision = 157680000 / $step; //5anos

		$opts[] = "-s";
		$opts[] = "$step";
		$opts[] = "DS:downstream:GAUGE:$heartbeat:U:U";
		if ($metric != 'rtt')
			$opts[] = "DS:upstream:GAUGE:$heartbeat:U:U";
		$opts[] = "RRA:AVERAGE:0.5:1:$mainPrecision"; //step por step
		$opts[] = "RRA:AVERAGE:0.5:4:$secondaryPrecision"; //a cada 4 steps
		$opts[] = "RRA:AVERAGE:0.5:16:$thirdPrecision"; //a cada 16 steps
		$opts[] = "RRA:AVERAGE:0.5:32:$fourthPrecision"; //a cada 32 steps
		$opts[] = "RRA:AVERAGE:0.5:64:$fifthPrecision"; //a cada 100 steps
		$opts[] = "RRA:MAX:0.5:1:$mainPrecision";
		$opts[] = "RRA:MAX:0.5:4:$secondaryPrecision";
		//$opts[] = "RRA:MAX:0.5:16:$thirdPrecision";
		//$opts[] = "RRA:MAX:0.5:100:$fourthPrecision";
		//$opts[] = "RRA:MIN:0.5:1:$mainPrecision";
		//$opts[] = "RRA:MIN:0.5:4:$secondaryPrecision";
		//$opts[] = "RRA:MIN:0.5:16:$thirdPrecision";
		//$opts[] = "RRA:MIN:0.5:100:$fourthPrecision";
		Fire::group('Created RRD Files: ');
		$path = $this->path();
		if (!is_dir($path)) {
			Fire::info('Creating Directory ' . $path);
			mkdir($path, 0774, true);
		}
		foreach ($this->types[0] as $l1)
			foreach ($this->types[1] as $l2) {
				$filename = $this->filename($metric, $l1 . $l2);
				Fire::info($path.$filename);
				$ret = rrd_create($path . $filename, $opts, count($opts));

				if ($ret == 0) {
					Fire::error($opts, 'RRD File Create Error: ' . rrd_error());
					Log::instance()->add(Log::ERROR, "RRD File Create Error: $path.$filename", $opts);
					$this->errors = true;
				} else {
					Log::instance()->add(Log::INFO, "RRD File Created $path$filename with $step second step");
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
	public function update($metric, array $data, $timestamp = 'N') {
		if ($timestamp == 'N') {
			$ts = date("d.m.Y H:i:s T");
			$timestamp = date("U");
		} else $ts = date("d.m.Y H:i:s T", $timestamp);
		Fire::group("Updating RRD Files - S:$this->source D:$this->destination TS:$ts");
		$path = $this->path();
		foreach ($this->types[0] as $l1)
			foreach ($this->types[1] as $l2) {
				$filename = $this->filename($metric, $l1 . $l2);
				$upstream = $data[$l1 . 'DS' . $l2];
				if ($metric != 'rtt') {
					$downstream = $data[$l1 . 'SD' . $l2];
					//Fire::info("$filename TIME $timestamp : DS $downstream : SD $upstream");
					$numbers = "SD $downstream : DS $upstream";
					$ret = rrd_update($path . $filename, "$timestamp:$downstream:$upstream");
				} else {
					//Fire::info("$filename TIME $timestamp : RTT $downstream");
					$ret = rrd_update($path . $filename, "$timestamp:$upstream");
					$numbers = "RTT $upstream";
				}

				Fire::info("RRD Update : TIME $timestamp : $numbers on $path$filename");

				if ($ret == 0) {
					$erf = rrd_error();
					Log::instance()->add(Log::WARNING, "RRD Update Failed :: $filename TIME $timestamp : $numbers :: " . $erf);
					Fire::error(array($path . $filename, $numbers), 'RRD Update Failed: ' . $erf);
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
	 * @return string
	 */
	public function graph($metric, $start, $end) {
		$rrdPath = $this->path();
		$path = $this->imgPath();

		Fire::group("Creating RRD $metric graph from $this->source to $this->destination, metric $metric", array('Collapsed' => "true"));
		if (!is_dir($path)) {
			Fire::info('Creating Directory ' . $path);
			mkdir($path, 0774, true);
		}
		$measures = Kohana::config("measure.$metric");

		//if(!$start) $start = date('d.m.Y H:i',mktime(date('H'), date('i'), date('s'), date("m") , date("d") - 1, date("Y")));
		//if(!$end) $end = date('d.m.Y H:i');

		Fire::info("Fetched range from $start to $end");

		$choosenMeasure = $measures['default'];
		$choosenView = __($measures['view']);
		$choosenFactor = $measures['factor'];

		foreach ($this->types[0] as $l1)
			foreach ($this->types[1] as $l2) {
				/**
				 * Opcoes da geracao do RRD
				 */
				$rrdfn = $filename = $this->filename($metric, $l1 . $l2);
				$title = "<b>" . ucfirst($metric) . " " . __($l2) . "</b> <small>($start até $end)</small>\r";

				if ($metric != 'rtt')
					$opts = array("-s $start", "-e $end", "-t $title ", "-P", "-v $choosenView", "-E",
						"-w 800", "-h 200",
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
					$opts = array("-s $start", "-e $end", "-t $title ", "-P", "-v $choosenView", "-E",
						"-w 800", "-h 200",
						"DEF:ds=$rrdPath$filename:downstream:AVERAGE",
						"LINE2:ds#990000:Roundtrip Time",
						"CDEF:dsm=ds,$choosenFactor,*",
						"COMMENT:\\n",
						"GPRINT:ds:AVERAGE:Pto Méd RTT\: %6.2lf %S$choosenMeasure",
						"COMMENT:  ",
						"GPRINT:ds:MAX:Pto Máx RTT\: %6.2lf %S$choosenMeasure\\r"
					);


				$filename = $this->filename($metric, $l1 . $l2, 'png');
				$imgs[] = $this->imgSrc($metric, $l1 . $l2);
				Fire::info($this->imgSrc($metric, $l1 . $l2));
				$ret = rrd_graph($path . $filename, $opts, count($opts));

				if (!is_array($ret)) {
					Fire::error($opts, 'RRD Graph File Create Error: ' . rrd_error());
					Log::instance()->add(Log::ERROR, "RRD Graph Create Error: $path$filename");
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
			return $matches[3] . $matches[2] . $matches[1];
		return false;
	}

	public function xml($metric, $start, $end, $m = 'Avg') {
		$path = $this->path();

		Fire::group("Creating RRD $metric xml from $this->source to $this->destination, metric $metric", array('Collapsed' => "true"));
		$measures = Kohana::config("measure.$metric");

		//if(!$start) $start = date('d.m.Y H:i',mktime(date('H'), date('i'), date('s'), date("m") , date("d") - 1, date("Y")));
		//if(!$end) $end = date('d.m.Y H:i');

		Fire::info("Fetched range from $start to $end");

		/**
		 * Opcoes da geracao do RRD
		 */
		$filename = $this->filename($metric, "Last" . $m);
		$verboseMeasure = __($measures['view']);

		$options = "-s \"$start\" -e \"$end\" DEF:sd=$path$filename:downstream:AVERAGE XPORT:sd:\"$metric ds\"";

		if ($metric != 'rtt') $options .= " DEF:ds=$path$filename:upstream:AVERAGE XPORT:ds:\"$metric sd\"";
		$resp = array();
		$ret = exec("rrdtool xport $options", $resp, $code);
		Fire::info("Xport Code: $code Last Line: $ret Command: rrdtool xport $options");
		Fire::groupEnd();
		return implode("\n", $resp);
	}

	public function json($metric, $start, $end, $m = 'Avg') {
		$xml = $this->xml($metric, $start, $end, $m);
		return Zend_Json::fromXml($xml);
	}

	public function last($metric, $m = 'Avg') {
		$path = $this->path();
		$filename = $this->filename($metric, "Last" . $m);
		return exec("rrdtool last $path$filename", $resp, $code);
	}

	public static function sci2num($value) {
		$float = sprintf('%f', $value);
		$integer = sprintf('%d', $value);
		if ($float == $integer) {
		// this is a whole number, so remove all decimals
			$output = $integer;
		} else {
		// remove trailing zeroes from the decimal portion
			$output = rtrim($float,'0');
		}

		return $output;
	}
}
