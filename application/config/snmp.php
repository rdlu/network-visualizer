<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'linuxManager' => array(
		'version'=>'.1.3.6.1.2.1.1.1.0'
	),
    'throughput' => array(
        'LastDSMax' => '.1.3.6.1.2.1.2.2.1.10.2',
        'LastSDMax' => '.1.3.6.1.2.1.2.2.1.16.2',
        'LastDSMin' => '.1.3.6.1.2.1.2.2.1.10.2',
        'LastSDMin' => '.1.3.6.1.2.1.2.2.1.16.2',
        'LastDSAvg' => '.1.3.6.1.2.1.2.2.1.10.2',
        'LastSDAvg' => '.1.3.6.1.2.1.2.2.1.16.2',
    ),


    //Set
    'agentTable' => array(
	    'entryStatus' => array(
            'oid'=>NMMIB.'.0.0.9.id',
            'type'=>'int'
       ),
       'ipaddress' => array(
	       'type' => 'string',
          'oid' => NMMIB.'.0.0.0.id',
       ),
       'name' => array(
	       'type' => 'string',
          'oid' => NMMIB.'.0.0.4.id',
       ),
	    'city' => array(
		    'oid' => NMMIB.'.0.0.5.id',
          'type' => 'string'
	    ),
       'state' => array(
	       'oid' => NMMIB.'.0.0.6.id',
          'type' => 'string',
       )
    ),
    'profileTable'=> array(
	     'entryStatus' => array(
            'oid'=>NMMIB.'.1.0.14.id',
            'type'=>'int'
        ),
        'polling' => array(
            'type' => 'int',
            'oid'=>NMMIB.'.1.0.0.id'
        ),
        'timeout' => array(
            'type' => 'int',
            'oid' => NMMIB.'.1.0.1.id', 
        ),
        'count' => array(
            'oid'=>NMMIB.'.1.0.2.id',
            'type' => 'int',
        ),
        'probeCount' => array(
            'oid' => NMMIB.'.1.0.3.id',
            'type' => 'int'
        ),
        'probeSize' => array(
            'oid' => NMMIB.'.1.0.4.id',
            'type' => 'int'
        ),
        'gap' => array(
            'oid'=>NMMIB.'.1.0.5.id',
            'type' => 'int'
        ),
        'qosValue' => array(
            'oid'=>NMMIB.'.1.0.6.id',
            'type' => 'int'
        ),
        'metrics' => array(
            'type' => 'ssv',
            'origin' => 'name',
            'oid' => NMMIB.'.1.0.10.id'
        )
    ),
    'managerTable' => array(
        'managerEntryStatus' => array(
            'oid'=>NMMIB.'.10.0.3.id',
            'type'=>'int'
        ),
        'managerAddress' => array(
            'oid'=>NMMIB.'.10.0.0.id',
            'type' => 'string'
        ),
        'managerPort' => array(
            'oid'=>NMMIB.'.10.0.1.id',
            'type' => 'int',
            'default' => 12000
        ),
        'managerProtocol' => array(
            'oid'=>NMMIB.'.10.0.2.id',
            'type'=>'int',
            'default'=>0 //UDP
        )
    )
);