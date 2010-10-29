<?php defined('SYSPATH') or die('No direct script access.');

return array(

	'dscp' => array(
        0 =>'Best Effort',
		46=>'High Priority',
        10=>'AF11',
        12=>'AF12',
        14=>'AF13',
        18=>'AF21',
        20=>'AF22',
        22=>'AF23',
        26=>'AF31',
        28=>'AF32',
        30=>'AF33',
        34=>'AF41',
        38=>'AF43'
	),
    'tos-dtr'=>array(
        0=>'Normal Delivery',
        1=>'Minimize Cost',
        2=>'Maximize Reliability',
        4=>'Maximize Throughput',
        8=>'Minimize Delay'
    ),
    'tos-precedence'=>array(
        0=>'Best Effort',
        1=>'Priority',
        2=>'Imediate',
        3=>'Flash',
        4=>'Flash Override',
        5=>'Critical',
        6=>'Internetwork Control',
        7=>'Network Control'
    )

);