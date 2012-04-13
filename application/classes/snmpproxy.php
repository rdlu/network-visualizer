<?php

class SnmpProxy
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
     * Cria, ou retorna uma instancia SNMPProxy, para determinado endereço
     * @static
     * @param string $address
     * @param string $community
     * @param array $options
     * @return SnmpProxy
     */
    public static function instance($address = NULL,$community='suppublic',$options = array()) {
        if ($address === NULL) {
            // Use the default instance name
            $address = SnmpProxy::$default;
        }

        if (!isset(SnmpProxy::$instances[$address])) {
            $newinstance = new SnmpProxy();
            $newinstance->setAddress($address);
            $newinstance->community = (string) $community;

            foreach($options as $option) {
                $newinstance->$option = $option;
            }
            SnmpProxy::$instances[$address] = $newinstance;
        }

        return SnmpProxy::$instances[$address];
    }

    public function setAddress($address) {
        if(Valid::Ip($address)) {
            $this->address = $address;
        } elseif(Valid::hostname($address)) {
            $this->address = Network::getAddress($address);
        } else {
            throw new Exception("Invalid IP Address in SNMP Class $address .");
        }
    }

    /**
     * Funcao que busca um oid unico
     * @param $oid
     * @return null|string
     */
    public function getValue($oid) {
        try {

            //$response = snmp2_get($this->address,$this->community,$oid,$this->timeout,$this->retries);
            $ch = curl_init();
            $proxyAddress = Kohana::config('network.snmpproxy.address');

            $data = array('address' => $this->address,'oid' => $oid);

            curl_setopt($ch, CURLOPT_URL, 'http://'.$proxyAddress);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

            $response = curl_exec($ch);
            curl_close($ch);
        } catch (Exception $err) {
            $code = $err->getCode();
            $msg = $err->getMessage();
            if($code == 0) {
                $userError = "O host $this->address não responde ao SNMP";
            } else {
                $userError = "O host teve um erro no SNMP: $msg";
            }
            Kohana::$log->add(Log::ERROR,"Erro no snmpget para o ip $this->address, oid $oid, $msg");
            $this->setError($userError,$msg,'error');
            return null;
        }

        return $response;
    }

    public function setValue($oid,$type,$value) {
        try {

            $ch = curl_init();
            $proxyAddress = Kohana::config('network.snmpproxy.address');

            $data = array('address' => $this->address,'oid' => $oid, 'value' => $value, 'type' => $type);

            curl_setopt($ch, CURLOPT_URL, $proxyAddress);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            curl_close($ch);
        } catch (Exception $err) {
            $code = $err->getCode();
            $msg = $err->getMessage();
            if($code == 0) {
                $userError = "O host $this->address não responde ao SNMP";
            } else {
                $userError = "O host teve um erro no SNMP: $msg";
            }
            $this->setError($userError,$msg,'error');
            return null;
        }

        return $response;
    }

    public function group($name,$subst=array()) {
        if (!isset($this->groups[$name])) {
            $oids = Kohana::config('snmp.'.$name);

            if($oids === NULL) {
                throw new Kohana_Exception("Configuration node '$name' does not exist on snmp configuration file",array($name));
            }

            //Fire::group('SNMP Data from '.$this->address,array('Collapsed'=>'true'));

            //Assume resultados em null
            foreach($oids as $key => $oid) {
                $return[$key] = NULL;
            }

            foreach($oids as $key => $oid) {
                foreach ($subst as $k=>$v) {
                    $oid['oid'] = str_replace($k,$v,$oid['oid']);
                }

                //Tenta obter cada um dos resultados
                $data = $this->getValue($oid['oid']);
                $pos = strpos($data,':');
                $dt = substr($data,$pos+2);
                $dt = trim($dt,"\0\n\r \"");
                $return[$key] = $dt;
            }

            $this->groups[$name] = $return;
            //Fire::info($return)->groupEnd();
        } else $return = $this->groups[$name];

        return $return;
    }

    public function setGroup($name,array $values,$subst=null) {
        $oids = Kohana::config('snmp.'.$name);

        if($oids === NULL) {
            throw new Kohana_Exception("Configuration node '$name' does not exist on snmp configuration file",array($name));
        }

        if($subst===NULL) {
            $subst = array();
        }

        //Fire::group('SNMP Data on '.$this->address,array('Collapsed'=>'true'));
        //Fire::info($values,"Received array to be set via SNMP:");

        $data = array();
        foreach($oids as $key => $oid) {
            if(isset($oid['readonly']) && $oid['readonly']) continue;

            if(count($values) && isset($values[$key])) $value = $values[$key];
            elseif(isset($oid['default'])) $value = $oid['default'];
            else continue;

            foreach ($subst as $k=>$v) {
                $oid['oid'] = str_replace($k,$v,$oid['oid']);
            }

            switch($oid['type']) {
                case 'int':
                    $type = 'i';
                    $value = (int) $value;
                    break;
                case 'ssv':
                    $type = 's';
                    $arr = $value;
                    $value = '';
                    $f=true;
                    foreach($arr as $v) {
                        if(!$f) $value .= " ";
                        $value .= $v->$oid['origin'];;
                        $f=false;
                    }
                    break;
                default:
                    $type = 's';
                    $value = (string) utf8::transliterate_to_ascii($value);
            }

            //Fire::info("$key: $value");

            try {
                $result = snmp2_set($this->address,$this->community,$oid['oid'],$type,$value,$this->timeout,$this->retries);
            } catch (Exception $err) {
                $code = $err->getCode();
                $msg = $err->getMessage();                $oe = $oid['oid'];
                //Fire::error($err,"Exception on SNMP SET $code");
                if($key == 'entryStatus' || $key == 'managerEntryStatus') {

                } else {
                    Kohana::$log->add(Log::ERROR,"Erro no snmpset para o ip $this->address, oid $key, valor $value, $msg");
                    $data[$key] = $msg;
                    break;
                }
            }
        }

        //Fire::groupEnd();
        return $data;
    }

    public static function convertTimestamp($string) {
        $str = explode(')',$string);
        if(count($str)>1)
            return trim($str[0],"() \t\n\r\0");
        else
            //return 'N';
            return date('U');
    }

    public function getOid($string) {
        return Kohana::config('snmp.'.$string);
    }

    public function getEntryOid($type = 'profile') {
        return $this->getOid($type.'Table.entryStatus');
    }

    public function getErrors() {
        return $this->errors;
    }

    protected function setError($userError,$logError,$class) {
        Kohana::$log->add(Log::ERROR,$logError);
        $arr['message'] = $userError;
        $arr['class'] = $class;
        $this->errors[] = $arr;
    }

    public function isReachable($oid) {
        return ($this->getValue($oid) == null)?false:true;
    }

    protected function isNoSuchInstance($response) {
        if(preg_match('/^No Such/',$response) || preg_match('/^Such/',$response)) {
            return true;
        }
        return false;
    }

    public function isResponding() {
        $response = $this->getValue(NMMIB.'.20.0');
        if($response == null || $this->isNoSuchInstance($response)) {
            return false;
        }
        return true;
    }

    /**
     * isNotLoaded: Funcao exclusiva para teste do EntryStatus do Netmetric SNMP
     * @param $response
     * @return bool
     */
    public function isNotLoaded($response) {
        if($this->isNotSet($response) || $response != '1') {
            return true;
        }
        return false;
    }

    /**
     * isProfileNotLoaded Funcao exclusiva para testar se um perfil está configurado em um determinado agente
     * @param int $profileId
     * @return bool
     */
    public function isProfileNotLoaded($profileId) {
        $response = $this->getValue(NMMIB.".1.0.14.$profileId");
        return $this->isNotLoaded($response);
    }

    public function isAgentNotLoaded($destinationId) {
        $response = $this->getValue(NMMIB.".0.0.9.$destinationId");
        return $this->isNotLoaded($response);
    }

    public function isNotSet($response) {
        if(preg_match('/^notSet/',$response)) {
            return true;
        }
        return false;
    }

    public function isValidResponse($response) {
        if(preg_match('/^notSet/',$response)) {
            return true;
        }
        return false;
    }
}
