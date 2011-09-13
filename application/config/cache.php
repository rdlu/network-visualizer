<?php defined('SYSPATH') or die('No direct script access.');
return array
(
	'default' => array
	(
		'driver' => 'memcache',
		'default_expire' => 3600,
		'compression' => false,
		'servers' => array(
			array(
				'host' => 'localhost', // Memcache Server
				'port' => 11211, // Memcache port number
				'persistent' => TRUE, // Persistent connection
			),
		),

	),
);