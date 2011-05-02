<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Arquivo que regula quais unidades de medida são usadas em cada um dos
 */

return array(
    'throughput' => array(
        'default' => 'bps',
        'view' => '',
	     'factor' => 1,
	     'type' => 'bitpersecond'
    ),
	 'throughputTCP' => array(
        'default' => 'bps',
        'view' => '',
	     'factor' => 1,
	     'type' => 'bitpersecond'
    ),
    'capacity' => array(
        'default' => 'bps',
        'view' => '',
	     'factor' => 1,
	     'type' => 'bitpersecond'
    ),
    'jitter' => array(
        'default' => 's',
        'view' => 'Em segundos',
	     'factor' => 1,
	     'type' => 'milisseconds'
    ),
    'loss' => array(
        'default' => "%%",
        'view' => "Porcentagem de perda",
	     'factor' => 1,
	     'type' => 'perc'
    ),
    'mos' => array(
        'default' => '',
        'view' => 'Mean Opinion Score, valor absoluto',
	     'factor' => 1,
	     'type' => false
    ),
    'owd' => array(
        'default' => 's',
        'view' => 'Em segundos',
	     'factor' => 1,
	     'type' => 'milisseconds'
    ),
    'pom' => array(
        'default' => '%%',
        'view' => '%%',
	     'factor' => 1,
	     'type' => 'perc'
    ),
    'rtt' => array(
        'default' => 's',
        'view' => 'Em segundos',
	     'factor' => 1,
	     'type' => 'milisseconds'
    ),
);