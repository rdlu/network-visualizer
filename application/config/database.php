<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
	'default' => array
	(
		'type'       => 'mysql',
		'connection' => array(
			/**
			 * The following options are available for MySQL:
			 *
			 * string   hostname     server hostname, or socket
			 * string   database     database name
			 * string   username     database username
			 * string   password     database password
			 * boolean  persistent   use persistent connections?
			 *
			 * Ports and sockets may be appended to the hostname.
			 */
			'hostname'   => 'julia',
			'database'   => 'mom_dev',
			'username'   => 'mom',
			'password'   => 'upY3tsLrHSszHtwC',
			'persistent' => FALSE,
		),
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
		'profiling'    => TRUE,
	),
	'production' => array
	(
		'type'       => 'mysql',
		'connection' => array(
			'hostname'   => 'localhost',
			'database'   => 'mom',
			'username'   => 'mom',
			'password'   => 'upY3tsLrHSszHtwC',
			'persistent' => FALSE,
		),
		'table_prefix' => '',
		'charset'      => 'utf8',
		'caching'      => FALSE,
		'profiling'    => TRUE,
	),
);