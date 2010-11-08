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
        $oids = Kohana::config('snmp.'.$name);

        Fire::group('SNMP to be fetched from '.$this->address)->info($oids)->groupEnd();

        foreach($oids as $key => $oid) {
            try {
                $return[$key] = snmp2_get($this->address,$this->community,$oid,1000,1);
            } catch(Exception $e) {
                $return[$key] = NULL;
            }

        }

        return $return;
    }
}
