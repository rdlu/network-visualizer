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
				if($newinstance->sonda->updated + 1800 > date("U") ) {
					$newinstance->sonda->status = 1;
					$newinstance->message = 'Entidade ativa e funcional';

					$newinstance->class = 'success';
				} elseif($newinstance->sonda->updated + 1800 <= date("U") ) {
					$newinstance->sonda->status = 2;
					$newinstance->class = 'warn';
					$newinstance->message = 'Entidade em estado desconhecido, não responde há mais de 30 minutos.';
				}

				if($newinstance->sonda->updated + 7200 <= date("U") ) {
					$newinstance->sonda->status = 3;
					$newinstance->class = 'error';
					$newinstance->message = 'Entidade em estado de erro passivo, não responde há mais de 2 horas.';
				}

				if($snmp) {
					$sncfg = Kohana::config('snmp.linuxManager.version');
					$sn = Snmp::instance($newinstance->sonda->ipaddress)->isReachable($sncfg['oid']);
					if(!$sn) {
						$newinstance->sonda->status = 3;
						$newinstance->class = 'error';
						$newinstance->message = 'Entidade em estado de erro ativo, não responde ao SNMP.';
					}
				}

				try {
					$newinstance->sonda->update();
				} catch(Validate_Exception $e) {
					Kohana::$log->add('ERROR',"O status da sonda $id não pode ser atualizado com sucesso. (Validate_Exception on Sonda::instance)");
					Fire::info($e->array->errors());
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
}
