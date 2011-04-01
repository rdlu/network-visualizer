<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rodrigo
 * Date: 3/31/11
 * Time: 1:04 PM
 */

class Pair {

	protected static $instances;

	protected $source;
	protected $destination;
	protected $profiles;
	protected $metrics;

	public static function instance($sourceId, $destinationId, $options = array()) {
		$sourceId = (int) $sourceId;
		$destinationId = (int) $destinationId;

		if (!isset(Pair::$instances[$sourceId][$destinationId])) {
			$newinstance = new Pair();
			$newinstance->source = Sprig::factory('entity', array('id' => $sourceId))->load();
			$newinstance->destination = Sprig::factory('entity', array('id' => $destinationId))->load();
			$newinstance->profiles = Sprig::factory('profile', array('destination_id' => $destinationId, 'source_id'=>$sourceId))->load();

			Fire::log($newinstance->profiles);

			foreach ($options as $option) {
				$newinstance->$option = $option;
			}
			Pair::$instances[$sourceId][$destinationId] = $newinstance;
		}

		return Pair::$instances[$sourceId][$destinationId];
	}

	public function getMetrics() {
		
	}
}
