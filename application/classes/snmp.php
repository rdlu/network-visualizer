<?php

class Snmp {

    protected $default = '127.0.0.1';

    protected static $instances = array();

    protected $groups = array();

    protected $address;

    protected $community;

    /**
     * @static
     * @param string $address
     * @param string $community
     * @return Snmp
     */
	public static function instance($address = NULL,$community='public') {
		if ($address === NULL) {
			// Use the default instance name
			$address = Snmp::$default;
		}

		if (!isset(Snmp::$instances[$address])) {
            $newinstance = new Snmp();
            $newinstance->setAddress($address);
            $newinstance->community = (string) $community;
            Snmp::$instances[$address] = $newinstance;
		}

		return Snmp::$instances[$address];
	}

    public function setAddress($address) {
        if(Validate::Ip($address)) {
            $this->address = $address;
        } else {
            throw new Kohana_Exception('Invalid IP Address in SNMP Class',$address);
        }
    }

    public function group($name) {
        if (!isset($this->groups[$name])) {
            $oids = Kohana::config('snmp.'.$name);

            if($oids === NULL) {
                throw new Kohana_Exception("Configuration node '$name' does not exist on snmp configuration file",array($name));
            }

            Fire::group('SNMP Data from '.$this->address,array('Collapsed'=>'true'))->group('To be fetched: ')->info($oids)->groupEnd();

            foreach($oids as $key => $oid) {
                try {
                    $data = snmp2_get($this->address,$this->community,$oid,100000,2);
                    $pos = strpos($data,':');
                    $dt = substr($data,$pos+2);
                    $return[$key] = $dt;
                } catch(Exception $e) {
                    $return[$key] = NULL;
                }
            }
            $this->groups[$name] = $return;
            Fire::group('Results: ')->info($return)->groupEnd()->groupEnd();
        } else $return = $this->groups[$name];

        return $return;
    }
}
