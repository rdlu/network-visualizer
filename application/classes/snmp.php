<?php

class Snmp_Exception extends Exception
{
}

class Snmp
{

    protected $default = '127.0.0.1';

    protected $timeout = 2000000;

    protected $retries = 2;

    protected static $instances = array();

    protected $groups = array();

    protected $address;

    protected $community;

    protected $errors = array();

    /**
     * Cria, ou retorna uma instancia SNMP, para determinado endereço
     * @static
     * @param string $address
     * @param string $community
     * @param array $options
     * @return Snmp
     */
    public static function instance($address = NULL, $community = 'suppublic', $options = array())
    {
        if (Kohana::$config->load('network.snmpproxy.active')) {
            return SnmpProxy::instance($address, $community, $options);
        }

        if ($address === NULL) {
            // Use the default instance name
            $address = Snmp::$default;
        }

        if (!isset(Snmp::$instances[$address])) {
            $newinstance = new Snmp();
            $newinstance->setAddress($address);
            $newinstance->community = (string)$community;

            foreach ($options as $option) {
                $newinstance->$option = $option;
            }
            Snmp::$instances[$address] = $newinstance;
        }

        return Snmp::$instances[$address];
    }

    /**
     * Funcao que busca um oid unico
     * @param $oid
     * @return null|string
     */
    public function getValue($oid)
    {
        $response = $this->getRawValue($oid);
        $pos = strpos($response, ':');
        $type = substr($response, 0, $pos);
        $value = substr($response, $pos + 1, strlen($response));
        $typedValue = null;

        switch ($type) {
            case 'INTEGER':
                $typedValue = (int)$value;
                break;
            default:
                $typedValue = (string)$value;
                break;
        }
        finfo("SNMP GET para " . $this->address, "OID " . $oid, $response, $typedValue, $pos, $type, $value);
        return $typedValue;
    }

    public function getRawValue($oid)
    {
        try {
            $response = snmp2_get($this->address, $this->community, $oid, $this->timeout, $this->retries);
        } catch (Exception $err) {
            $code = $err->getCode();
            $msg = $err->getMessage();
            if ($code == 0) {
                $userError = "O host $this->address não responde ao SNMP";
            } else {
                $userError = "O host teve um erro no SNMP: $msg";
            }
            $this->setError($userError, $msg, 'error');
            return null;
        }
        return $response;
    }

    public function isNotSet($response)
    {
        if (preg_match('/^notSet/', $response)) {
            return true;
        }
        return false;
    }

    /**
     * isNotLoaded: Funcao exclusiva para teste do EntryStatus do Netmetric SNMP
     * @param $response
     * @return bool
     */
    public function isNotLoaded($response)
    {
        if ($this->isNotSet($response) || $response != '1' || $response != 1) {
            return true;
        }
        return false;
    }

    /**
     * isProfileNotLoaded Funcao exclusiva para testar se um perfil está configurado em um determinado agente
     * @param int $profileId
     * @return bool
     */
    public function isProfileNotLoaded($profileId)
    {
        $response = $this->getValue(NMMIB . ".1.0.14.$profileId");
        finfo("Profile $profileId exists?", $response, !$this->isNotLoaded($response));
        return $this->isNotLoaded($response);
    }

    public function isAgentNotLoaded($destinationId)
    {
        $response = $this->getValue(NMMIB . ".0.0.9.$destinationId");
        return $this->isNotLoaded($response);
    }

    public function isResponding()
    {
        $response = $this->getValue(NMMIB . '.20.0');
        if ($response == null || $this->isNoSuchInstance($response)) {
            return false;
        }
        return true;
    }

    protected function isNoSuchInstance($response)
    {
        if (preg_match('/^No Such/', $response)) {
            return true;
        }
        return false;
    }

    public function isReachable($oid)
    {
        return ($this->getValue($oid) == null) ? false : true;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    protected function setError($userError, $logError, $class)
    {
        Kohana::$log->add(Log::ERROR, $logError);
        $arr['message'] = $userError;
        $arr['class'] = $class;
        $this->errors[] = $arr;
    }

    public function setGroup($name, array $values, $subst = null)
    {
        $oids = Kohana::$config->load('snmp.' . $name);

        if ($oids === NULL) {
            throw new Kohana_Exception("Configuration node '$name' does not exist on snmp configuration file", array($name));
        }

        if ($subst === NULL) {
            $subst = array();
        }

        //Fire::group('SNMP Data on '.$this->address,array('Collapsed'=>'true'));
        //Fire::info($values,"Received array to be set via SNMP:");

        $data = array();
        foreach ($oids as $key => $oid) {
            if (isset($oid['readonly']) && $oid['readonly']) continue;

            if (count($values) && isset($values[$key])) $value = $values[$key];
            elseif (isset($oid['default'])) $value = $oid['default'];
            else continue;

            foreach ($subst as $k => $v) {
                $oid['oid'] = str_replace($k, $v, $oid['oid']);
            }

            switch ($oid['type']) {
                case 'int':
                    $type = 'i';
                    $value = (int)$value;
                    break;
                case 'ssv':
                    $type = 's';
                    $arr = $value;
                    $value = '';
                    $f = true;
                    foreach ($arr as $v) {
                        if (!$f) $value .= " ";
                        $value .= $v->$oid['origin'];
                        ;
                        $f = false;
                    }
                    break;
                default:
                    $type = 's';
                    $value = (string)utf8::transliterate_to_ascii($value);
                    break;
            }

            //Fire::info("$key: $value");

            try {
                $result = snmp2_set($this->address, $this->community, $oid['oid'], $type, $value, $this->timeout, $this->retries);
                //fwarn("SNMP set para $name ".$this->address,"OID ".$oid['oid']." $value",$result);

            } catch (Exception $err) {
                $code = $err->getCode();
                $msg = $err->getMessage();
                $oe = $oid['oid'];
                //Fire::error($err,"Exception on SNMP SET $code");
                if ($key == 'entryStatus' || $key == 'managerEntryStatus') {

                } else {
                    $this->setError("Erro no snmpset para o ip $this->address, oid $key, valor $value, $msg", "Erro no snmpset para o ip $this->address, oid $key, valor $value, $msg", 'error');
                    $data[$key] = $msg;
                    break;
                }
            }
        }

        //Fire::groupEnd();
        return $data;
    }

    public function setAddress($address)
    {
        if (Valid::Ip($address)) {
            $this->address = $address;
        } elseif (Valid::hostname($address)) {
            $this->address = Network::getAddress($address);
        } else {
            throw new Exception("Invalid IP Address in SNMP Class $address .");
        }
    }

    public function group($name, $subst = array())
    {
        if (!isset($this->groups[$name])) {
            $oids = Kohana::$config->load('snmp.' . $name);

            if ($oids === NULL) {
                throw new Kohana_Exception("Configuration node '$name' does not exist on snmp configuration file", array($name));
            }

            //Fire::group('SNMP Data from '.$this->address,array('Collapsed'=>'true'));

            //Assume resultados em null
            foreach ($oids as $key => $oid) {
                $return[$key] = NULL;
            }

            foreach ($oids as $key => $oid) {
                foreach ($subst as $k => $v) {
                    $oid['oid'] = str_replace($k, $v, $oid['oid']);
                }

                //Tenta obter cada um dos resultados
                try {
                    $data = snmp2_get($this->address, $this->community, $oid['oid'], $this->timeout, $this->retries);
                    //Fire::info($oid['oid']);
                    $pos = strpos($data, ':');
                    $dt = substr($data, $pos + 2);
                    $dt = trim($dt, "\0\n\r \"");
                    $return[$key] = $dt;
                } catch (Exception $e) {
                    $return[$key] = NULL;
                    $code = $e->getCode();
                    $msg = $e->getMessage();
                    //Fire::error($e,"Exception on SNMP GET $code");
                    Kohana::$log->add(Log::ERROR, "Erro no snmpget para o ip $this->address, oid $key, $msg");
                    break;
                }
            }

            $this->groups[$name] = $return;
            //Fire::info($return)->groupEnd();
        } else $return = $this->groups[$name];

        return $return;
    }

    public static function convertTimestamp($string)
    {
        $str = explode(')', $string);
        if (count($str) > 1)
            return trim($str[0], "() \t\n\r\0");
        else
            //return 'N';
            return date('U');
    }

    public function getOid($string)
    {
        return Kohana::$config->load('snmp.' . $string);
    }

    public function getEntryOid($type = 'profile')
    {
        return $this->getOid($type . 'Table.entryStatus');
    }
}
