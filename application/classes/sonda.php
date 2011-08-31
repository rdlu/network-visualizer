<?php

class Sonda {

	protected static $instances = array();

	protected $sonda;

	protected $status = array(
		0=>'Inativo',
		1=>'Ativo',
		2=>'Alerta',
		3=>'Erro',
		-1=>'Bloqueado',
	);

	protected $message = "Entidade desativada.";
	protected $class = "info";
	protected $version = array('version'=>null);

	/**
	 * @static
	 * @param  $id
	 * @param bool $snmp
	 * @return object Sonda
	 */
	public static function instance($id,$snmp=false) {
		if (!isset(Sonda::$instances[$id])) {
			$newinstance = new Sonda();
			if(isset($sonda)) $newinstance->sonda = $sonda;
			else $newinstance->sonda = Sprig::factory('entity',array('id'=>$id))->load();
			//update
			if($newinstance->sonda->status!=0) {
				//Assume erro e testa as alternativas
				$newinstance->class = 'error';
				$newinstance->message = 'Entidade em estado de erro ativo, não responde ao SNMP.';
				
				if($newinstance->sonda->updated + 1800 > date("U") ) {
					$newinstance->sonda->status = 1;
					$newinstance->message = 'Entidade ativa e funcional';

					$newinstance->class = 'success';
				} elseif($newinstance->sonda->updated + 300 <= date("U") ) {
					$newinstance->sonda->status = 2;
					$newinstance->class = 'warn';
					$newinstance->message = 'A sonda não faz medições há mais de 5 minutos.';
				}

				/*if($newinstance->sonda->updated + 600 <= date("U") ) {
					$newinstance->sonda->status = 3;
					$newinstance->class = 'warn';
					$newinstance->message = 'A sonda não faz medições há mais de 10 minutos.';
				}*/

				if($snmp || ($newinstance->sonda->updated + 600 <= date("U") && $newinstance->sonda->status!=3)) {

					try {
						if(!$newinstance->checkStatus()) {
							$newinstance->sonda->status = 3;
							$newinstance->class = 'error';
							$newinstance->message = 'Entidade em estado de erro ativo, não responde ao SNMP.';
						}
					} catch (Network_Exception $err) {
						$newinstance->sonda->status = 4;
						$newinstance->class = 'error';
						$newinstance->message = 'Entidade fora do ar, não se registrou no DDNS';
					}
				}

				try {
					$newinstance->sonda->update();
				} catch(Validate_Exception $e) {
					Kohana::$log->add('ERROR',"O status da sonda $id não pode ser atualizado com sucesso. (Validate_Exception on Sonda::instance)");
					//Fire::info($e->array->errors());
				}

			}
			else {
				$newinstance->sonda->status = 0;
				$newinstance->message = 'Entidade desativada ou sem processos de medição';
			}
			Sonda::$instances[$id] = $newinstance;
		}

		return Sonda::$instances[$id];
	}
	/**
	 * @return int
	 */
	public function getCode() {
		return $this->sonda->status;
	}
	/**
	 * @return string
	 */
	public function getString() {
		return $this->status[$this->sonda->status];
	}

	public function getMessage() {
		return $this->message;
	}

	public function getClass() {
		return $this->class;
	}

	public function getVersion() {
		if(!$this->version['version']) {
			$realip = Network::getAddress($this->sonda->ipaddress);
			$this->version = Snmp::instance($realip)->group('linuxManager');
		}
		return $this->version;
	}

	public function checkStatus() {
		$version = $this->getVersion();
		foreach($version as $k => $v) {
			if($v == null) return false;
		}
		return true;
	}

	public function checkSNMP() {
		$realip = Network::getAddress($this->sonda->ipaddress);
      return Snmp::instance($realip)->isResponding();
	}
}
