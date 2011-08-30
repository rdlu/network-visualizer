<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'linuxManager' => array(
		'version' => array(
			'oid' => '.1.3.6.1.2.1.1.1.0',
			'type' => 'string',
			'readonly' => true
		),
		'nmVersion' => array(
			'oid' => NMMIB.'.20.0',
			'type' => 'string',
			'readonly' => true
		),
		'ddnsVersion' => array(
			'oid' => NMMIB.'.20.1',
			'type' => 'string',
			'readonly' => true
		),
		'gparcVersion' => array(
			'oid' => NMMIB.'.20.2',
			'type' => 'string',
			'readonly' => true
		),
		'modemInfo' => array(
			'oid' => NMMIB.'.20.3',
			'type' => 'string',
			'readonly' => true
		),
		'osVersion' => array(
			'oid' => NMMIB.'.20.4',
			'type' => 'string',
			'readonly' => true
		)
	),
    'throughput' => array(
	    'LastDSMax' => array(
		    'oid' => NMMIB.'.3.7.0.0.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastDSMin' => array(
		    'oid' => NMMIB.'.3.7.0.1.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastDSAvg' => array(
		    'oid' => NMMIB.'.3.7.0.2.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastSDMax' => array(
		    'oid' => NMMIB.'.3.7.0.3.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastSDMin' => array(
		    'oid' => NMMIB.'.3.7.0.4.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastSDAvg' => array(
		    'oid' => NMMIB.'.3.7.0.5.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSMax' => array(
		    'oid' => NMMIB.'.3.7.0.6.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSMin' => array(
		    'oid' => NMMIB.'.3.7.0.7.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSAvg' => array(
		    'oid' => NMMIB.'.3.7.0.8.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageSDMax' => array(
		    'oid' => NMMIB.'.3.7.0.9.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageSDMin' => array(
		    'oid' => NMMIB.'.3.7.0.10.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageSDAvg' => array(
		    'oid' => NMMIB.'.3.7.0.11.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
    ),
    'throughputTCP' => array(
	    'LastDSMax' => array(
		    'oid' => NMMIB.'.3.8.0.0.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastDSMin' => array(
		    'oid' => NMMIB.'.3.8.0.1.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastDSAvg' => array(
		    'oid' => NMMIB.'.3.8.0.2.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastSDMax' => array(
		    'oid' => NMMIB.'.3.8.0.3.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastSDMin' => array(
		    'oid' => NMMIB.'.3.8.0.4.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastSDAvg' => array(
		    'oid' => NMMIB.'.3.8.0.5.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSMax' => array(
		    'oid' => NMMIB.'.3.8.0.6.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSMin' => array(
		    'oid' => NMMIB.'.3.8.0.7.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSAvg' => array(
		    'oid' => NMMIB.'.3.8.0.8.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageSDMax' => array(
		    'oid' => NMMIB.'.3.8.0.9.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageSDMin' => array(
		    'oid' => NMMIB.'.3.8.0.10.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageSDAvg' => array(
		    'oid' => NMMIB.'.3.8.0.11.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
    ),

	'mos' => array(
	    'LastDSMax' => array(
		    'oid' => NMMIB.'.3.6.0.0.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastDSMin' => array(
		    'oid' => NMMIB.'.3.6.0.1.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastDSAvg' => array(
		    'oid' => NMMIB.'.3.6.0.2.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastSDMax' => array(
		    'oid' => NMMIB.'.3.6.0.3.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastSDMin' => array(
		    'oid' => NMMIB.'.3.6.0.4.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastSDAvg' => array(
		    'oid' => NMMIB.'.3.6.0.5.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSMax' => array(
		    'oid' => NMMIB.'.3.6.0.6.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSMin' => array(
		    'oid' => NMMIB.'.3.6.0.7.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSAvg' => array(
		    'oid' => NMMIB.'.3.6.0.8.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageSDMax' => array(
		    'oid' => NMMIB.'.3.6.0.9.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageSDMin' => array(
		    'oid' => NMMIB.'.3.6.0.10.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageSDAvg' => array(
		    'oid' => NMMIB.'.3.6.0.11.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
   ),
	'rtt' => array(
	    'LastDSMax' => array(
		    'oid' => NMMIB.'.3.5.0.0.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastDSMin' => array(
		    'oid' => NMMIB.'.3.5.0.1.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastDSAvg' => array(
		    'oid' => NMMIB.'.3.5.0.2.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSMax' => array(
		    'oid' => NMMIB.'.3.5.0.6.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSMin' => array(
		    'oid' => NMMIB.'.3.5.0.7.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSAvg' => array(
		    'oid' => NMMIB.'.3.5.0.8.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
    ),
	'pom' => array(
	    'LastDSMax' => array(
		    'oid' => NMMIB.'.3.4.0.0.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastDSMin' => array(
		    'oid' => NMMIB.'.3.4.0.1.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastDSAvg' => array(
		    'oid' => NMMIB.'.3.4.0.2.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastSDMax' => array(
		    'oid' => NMMIB.'.3.4.0.3.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastSDMin' => array(
		    'oid' => NMMIB.'.3.4.0.4.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastSDAvg' => array(
		    'oid' => NMMIB.'.3.4.0.5.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSMax' => array(
		    'oid' => NMMIB.'.3.4.0.6.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSMin' => array(
		    'oid' => NMMIB.'.3.4.0.7.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSAvg' => array(
		    'oid' => NMMIB.'.3.4.0.8.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageSDMax' => array(
		    'oid' => NMMIB.'.3.4.0.9.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageSDMin' => array(
		    'oid' => NMMIB.'.3.4.0.10.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageSDAvg' => array(
		    'oid' => NMMIB.'.3.4.0.11.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
   ),
	'owd' => array(
	    'LastDSMax' => array(
		    'oid' => NMMIB.'.3.3.0.0.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastDSMin' => array(
		    'oid' => NMMIB.'.3.3.0.1.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastDSAvg' => array(
		    'oid' => NMMIB.'.3.3.0.2.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastSDMax' => array(
		    'oid' => NMMIB.'.3.3.0.3.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastSDMin' => array(
		    'oid' => NMMIB.'.3.3.0.4.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastSDAvg' => array(
		    'oid' => NMMIB.'.3.3.0.5.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSMax' => array(
		    'oid' => NMMIB.'.3.3.0.6.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSMin' => array(
		    'oid' => NMMIB.'.3.3.0.7.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSAvg' => array(
		    'oid' => NMMIB.'.3.3.0.8.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageSDMax' => array(
		    'oid' => NMMIB.'.3.3.0.9.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageSDMin' => array(
		    'oid' => NMMIB.'.3.3.0.10.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageSDAvg' => array(
		    'oid' => NMMIB.'.3.3.0.11.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	),
	'jitter' => array(
	    'LastDSMax' => array(
		    'oid' => NMMIB.'.3.2.0.0.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastDSMin' => array(
		    'oid' => NMMIB.'.3.2.0.1.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastDSAvg' => array(
		    'oid' => NMMIB.'.3.2.0.2.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastSDMax' => array(
		    'oid' => NMMIB.'.3.2.0.3.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastSDMin' => array(
		    'oid' => NMMIB.'.3.2.0.4.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastSDAvg' => array(
		    'oid' => NMMIB.'.3.2.0.5.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSMax' => array(
		    'oid' => NMMIB.'.3.2.0.6.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSMin' => array(
		    'oid' => NMMIB.'.3.2.0.7.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSAvg' => array(
		    'oid' => NMMIB.'.3.2.0.8.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageSDMax' => array(
		    'oid' => NMMIB.'.3.2.0.9.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageSDMin' => array(
		    'oid' => NMMIB.'.3.2.0.10.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageSDAvg' => array(
		    'oid' => NMMIB.'.3.2.0.11.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	),
	'bandwith' => array(
	    'LastDSMax' => array(
		    'oid' => NMMIB.'.3.1.0.0.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastDSMin' => array(
		    'oid' => NMMIB.'.3.1.0.1.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastDSAvg' => array(
		    'oid' => NMMIB.'.3.1.0.2.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastSDMax' => array(
		    'oid' => NMMIB.'.3.1.0.3.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastSDMin' => array(
		    'oid' => NMMIB.'.3.1.0.4.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastSDAvg' => array(
		    'oid' => NMMIB.'.3.1.0.5.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSMax' => array(
		    'oid' => NMMIB.'.3.1.0.6.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSMin' => array(
		    'oid' => NMMIB.'.3.1.0.7.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSAvg' => array(
		    'oid' => NMMIB.'.3.1.0.8.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageSDMax' => array(
		    'oid' => NMMIB.'.3.1.0.9.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageSDMin' => array(
		    'oid' => NMMIB.'.3.1.0.10.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageSDAvg' => array(
		    'oid' => NMMIB.'.3.1.0.11.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	),
	'loss' => array(
	    'LastDSMax' => array(
		    'oid' => NMMIB.'.3.0.0.0.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastDSMin' => array(
		    'oid' => NMMIB.'.3.0.0.1.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastDSAvg' => array(
		    'oid' => NMMIB.'.3.0.0.2.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastSDMax' => array(
		    'oid' => NMMIB.'.3.0.0.3.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastSDMin' => array(
		    'oid' => NMMIB.'.3.0.0.4.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'LastSDAvg' => array(
		    'oid' => NMMIB.'.3.0.0.5.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSMax' => array(
		    'oid' => NMMIB.'.3.0.0.6.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSMin' => array(
		    'oid' => NMMIB.'.3.0.0.7.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageDSAvg' => array(
		    'oid' => NMMIB.'.3.0.0.8.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageSDMax' => array(
		    'oid' => NMMIB.'.3.0.0.9.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageSDMin' => array(
		    'oid' => NMMIB.'.3.0.0.10.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	    'AverageSDAvg' => array(
		    'oid' => NMMIB.'.3.0.0.11.id',
		    'type' => 'int',
		    'readonly' => true
	    ),
	),
	'agentSimple' => array(
		'ipaddress' => array(
	       'type' => 'string',
          'oid' => NMMIB.'.0.0.0.pid',
		),
		'timestamp' => array(
		    'type' => 'fuckTS',
		    'oid' => NMMIB.'.0.0.2.pid',
		    'readonly' => true
		),
	),


    //Set
    'agentTable' => array(
	    'entryStatus' => array(
            'oid'=>NMMIB.'.0.0.9.pid',
            'type'=>'int'
       ),
       'ipaddress' => array(
	       'type' => 'string',
          'oid' => NMMIB.'.0.0.0.pid',
       ),
	    'port' => array(
		    'type' => 'int',
		    'oid' => NMMIB.'.0.0.1.pid',
		    'default' => 12000
	    ),
       'name' => array(
	       'type' => 'string',
          'oid' => NMMIB.'.0.0.4.pid',
       ),
	    'city' => array(
		    'oid' => NMMIB.'.0.0.5.pid',
          'type' => 'string'
	    ),
       'state' => array(
	       'oid' => NMMIB.'.0.0.6.pid',
          'type' => 'string',
       ),
	    'priority' => array(
		    'type' => 'int',
		    'oid' => NMMIB.'.0.0.7.pid',
		    'default' => 1
	    ),
	    'profile' => array(
		    'type' => 'int',
		    'oid' => NMMIB.'.0.0.8.pid',
	    ),
	    'timestamp' => array(
		    'type' => 'fuckTS',
		    'oid' => NMMIB.'.0.0.2.pid',
		    'readonly' => true
	    ),
	    'msgError' => array(
		    'type' => 'string',
		    'oid' => NMMIB.'.0.0.12.pid',
		    'readonly' => true
	    ),
	    'status' => array(
		   'type' => 'int',
		    'oid' => NMMIB.'.0.0.10.pid',
		    'default' => 1
	    ),
	    'finalEntryStatus' => array(
		    'type' => 'int',
		    'oid' => NMMIB.'.0.0.9.pid',
		    'default' => 2
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
	     'IsExtendedPeriod' => array(
            'oid'=>NMMIB.'.1.0.8.id',
            'type' => 'int',
		      'default' => 0
        ),
	     'protocol' => array(
		     'oid' => NMMIB.'.1.0.9.id',
		     'type' => 'int',
		     'default' => 0
	     ),
        'metrics' => array(
            'type' => 'ssv',
            'origin' => 'plugin',
            'oid' => NMMIB.'.1.0.10.id'
        ),
	    'finalEntryStatus' => array(
            'oid'=>NMMIB.'.1.0.14.id',
            'type'=>'int',
		      'default' => 1
        ),
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
        ),
	    'finalEntryStatus' => array(
		    'type' => 'int',
		    'oid' => NMMIB.'.10.0.3.id',
		    'default' => 2
	    )
    ),
	'disableAgent' => array(
		'status' => array(
          'oid'=>NMMIB.'.0.0.10.id',
          'type'=>'int',
		    'default' => 0
       ),
	   'entryStatus' => array(
          'oid'=>NMMIB.'.0.0.9.id',
          'type'=>'int',
		    'default' => 2
       ),
	),
	'removeAgent' => array(
		'finalEntryStatus' => array(
          'oid'=>NMMIB.'.0.0.9.id',
          'type'=>'int',
		    'default' => 5
       ),
	),
	'removeManager' => array(
	    'entryStatus' => array(
		    'oid'=>NMMIB.'.10.0.3.id',
		    'type'=>'int',
		    'default' => 5
       ),
	)
);