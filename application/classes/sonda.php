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
	 * getDefaultManagerId actually returns the most used managaer
	 * @static
	 * @return int
	 */
	public static function getDefaultManagerId() {
		$processes = Database::instance()->query(Database::SELECT,"SELECT source_id,count(*) FROM processes GROUP BY source_id ORDER BY count(*) LIMIT 1");
		return $processes->get("source_id",1);
	}

	public static function getDefaultManager() {
		$id = self::getDefaultManagerId();
		return Sprig::factory('entity',array('id'=>$id))->load()->as_array();
	}

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
				//Se a ultima atualizacao foi ate 5 minutos atras
				$a = $newinstance->sonda->updated;
				$a2 = date("U") - 300;
				if($newinstance->sonda->updated > date("U") - 300) {
					$newinstance->sonda->status = 1;
					$newinstance->message = 'Entidade ativa e funcional';
					$newinstance->class = 'success';
				} else {
					//Se já estiver em erro emite erro direto
					if($newinstance->sonda->status == 3) {
							$newinstance->class = 'error';
							$newinstance->message = 'Entidade em estado de erro ativo, favor verificar se ela está online';
					} else {
						//Se estiver em alerta, tem a possibilidade de escalar para erro
						if($newinstance->sonda->updated + 600 <= date("U")) {
							//Consulta via SNMP para ter certeza
							try {
								if(!$newinstance->checkStatus()) {
									$newinstance->sonda->status = 3;
									$newinstance->class = 'error';
									$newinstance->message = 'Entidade em estado de erro ativo, não responde ao SNMP.';
								} else {
									//Consultou com sucesso
									$newinstance->sonda->status = 2;
									$newinstance->message = 'A sonda não envia resultados há mais de 5 minutos, mas responde ao SNMP';
									$newinstance->class = 'warn';
								}
								$snmp = false;
							} catch (Network_Exception $err) {
								$newinstance->sonda->status = 3;
								$newinstance->class = 'error';
								$newinstance->message = 'Entidade fora do ar, não se registrou no DDNS';
							}
						} else {
							$newinstance->sonda->status = 2;
							$newinstance->class = 'warn';
							$newinstance->message = 'A sonda não faz medições há mais de 5 minutos.';
						}
					}
				}
				
				if($snmp) {

					try {
						if(!$newinstance->checkStatus()) {
							$newinstance->sonda->status = 3;
							$newinstance->class = 'error';
							$newinstance->message = 'Entidade em estado de erro ativo, não responde ao SNMP.';
						} elseif($newinstance->sonda->updated > date("U") - 300) {
							//Consultou com sucesso
							$newinstance->sonda->status = 1;
							$newinstance->message = 'Entidade ativa e funcional';
							$newinstance->class = 'success';
						} else {
							//Consultou com sucesso, mas atualizacao atrasada
							$newinstance->sonda->status = 2;
							$newinstance->message = 'A sonda não envia resultados há mais de 5 minutos, mas responde ao SNMP';
							$newinstance->class = 'warn';
						}
					} catch (Network_Exception $err) {
						$newinstance->sonda->status = 3;
						$newinstance->class = 'error';
						$newinstance->message = 'Entidade fora do ar, não se registrou no DDNS';
					}
				}

				try {
					$newinstance->sonda->update();
				} catch(Validation_Exception $e) {
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

			try {
				$realip = Network::getAddress($this->sonda->ipaddress);
				$this->version = Snmp::instance($realip)->group('linuxManager');
			} catch (Exception $err) {
				foreach(Kohana::config('snmp.linuxManager') as $k => $v) {
					$this->version[$k] = null;
				}
			}

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
