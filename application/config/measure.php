<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Arquivo que regula quais unidades de medida são usadas em cada um dos
 */

return array(
    'throughput' => array(
        'default' => 'bps',
        'view' => 'bps'
    ),
    'bandwith' => array(
        'default' => 'bps',
        'view' => 'bps'
    ),
    'jitter' => array(
        'default' => 'µs',
        'view' => 'ms'
    ),
    'loss' => array(
        'default' => '%',
        'view' => '%'
    ),
    'mos' => array(
        'default' => '',
        'view' => ''
    ),
    'owd' => array(
        'default' => 's',
        'view' => 'ms'
    ),
    'pom' => array(
        'default' => '%',
        'view' => '%'
    ),
    'rtt' => array(
        'default' => 's',
        'view' => 'ms'
    ),
);