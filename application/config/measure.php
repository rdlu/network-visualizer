<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Arquivo que regula quais unidades de medida são usadas em cada um dos
 */

return array(
    'throughput' => array(
        'default' => 'bps',
        'view' => '',
	     'factor' => 1
    ),
    'bandwith' => array(
        'default' => 'bps',
        'view' => '',
	     'factor' => 1
    ),
    'jitter' => array(
        'default' => 's',
        'view' => 'Em segundos',
	     'factor' => 1
    ),
    'loss' => array(
        'default' => "%%",
        'view' => "Porcentagem de perda",
	     'factor' => 1
    ),
    'mos' => array(
        'default' => '',
        'view' => 'Mean Opinion Score, valor absoluto',
	     'factor' => 1
    ),
    'owd' => array(
        'default' => 's',
        'view' => 'Em segundos',
	     'factor' => 1
    ),
    'pom' => array(
        'default' => '%%',
        'view' => '%%',
	     'factor' => 1
    ),
    'rtt' => array(
        'default' => 's',
        'view' => 'Em segundos',
	     'factor' => 1
    ),
);