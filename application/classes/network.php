<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rodrigo
 * Date: 2/7/11
 * Time: 5:04 PM
 * To change this template use File | Settings | File Templates.
 */

class Network_Exception extends Exception {}

class Network {

	public static function getAddress($str) {
		if(Valid::ip($str)) return $str;
		$ddns = Kohana::config('network.ddns.server');
		$rtrim = rtrim(`/usr/bin/dig @$ddns $str A +short | /usr/bin/tail -1`);
		//Fire::info("DDNS Result for $str = $rtrim");
		if(strlen($rtrim) < 7) throw new Network_Exception("DDNS query returned no result for $str",1);
		if(strlen($rtrim) > 16) throw new Network_Exception("DDNS timeout. $ddns is online?",2);
		return $rtrim;
	}

}
