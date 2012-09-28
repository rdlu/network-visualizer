<?php
/**
 * Arquivo com as rotas do aplicativo
 */
Route::set('log', 'log(/<date>)', array('date' => '\d{4}/\d{2}/\d{2}',))
    ->defaults(array('controller' => 'log', 'action' => 'view'));

Route::set('log2', 'log')
    ->defaults(array('controller' => 'log', 'action' => 'view'));

Route::set('processNew', 'processes/new(/<source>)', array('source' => '[0-9]+'))
    ->defaults(array(
    'controller' => 'processes',
    'action' => 'new'
));

Route::set('processSetup', 'processes/setup(/<source>)', array('source' => '[0-9]+'))
    ->defaults(array(
    'controller' => 'processes',
    'action' => 'setup'
));

Route::set('processIndex', 'processes/list(/<source>)', array('source' => '[a-z0-9.]+'))
    ->defaults(array(
    'controller' => 'processes',
    'action' => 'list'
));

///mom/collect/id/274/kpis/7401/SonyEricsson/R800i/-/-/-101/Desconhecido/-/-/0/15/651/1348670516 passa
///mom/collect/id/205/kpis/0/-/-/-/-/Desconhecido/Desconhecido/-/-/0/Desconhecido/0/1348671392
///mom/collect/id/205/kpis/65535/unknown/sdk/-/UMTS/-99/Desconhecido/-/-/0/49/-1/1348752315
///mom/collect/id/274/kpis/33564/SonyEricsson/R800i/-/-/-111/Desconhecido/-/-/0/<1/41551/1348683968
///mom/collect/id/211/kpis/40126/ZTE/ZTEV860/-/HSDPA/-67/Desconhecido/-/-/0/326/42421/1348751841
///mom/collect/id/211/kpis/40126/ZTE/ZTEV860/-/HSDPA/-67/Desconhecido/-/-/0/451/42421/1348752729 HTTP/1.1
Route::set('kpis',
    'collect/id/<destination>/kpis/<cellID>/<brand>/<model>/<connType>/<connTech>/<signal>/<errorRate>/<numberOfIPs>/<route>/<mtu>/<dnsLatency>/<lac>/<timestamp>',
    array('destination' => '[0-9]+',
        'cellID' => '[0-9\-]+',
        'brand' => '.+', //'[0-9.]+', //'^[+-]?\d[.]?\d'
        'model' => '.+', //'[0-9.]+',
        'connType' => '[A-Za-z\-]+', //'[0-9.]+',
        'connTech' => '[A-Za-z\-]+',
        'signal' => '[>|<]*[0-9.\- ]+|Desconhecido|unknown', //em dbm
        'errorRate' => '[>|<]*[0-9.\- ]+|Desconhecido|unknown', //'[0-9.]+'
        'numberOfIPs' => '[0-9\-]+',
        'route' => '.+',
        'mtu' => '[0-9\-]+',
        'dnsLatency' => '[0-9.\- ><]+|Desconhecido',
        'lac' => '[-]*[0-9 ><]+',
        'timestamp' => '[0-9]+'
    ))
    ->defaults(array(
    'controller' => 'collect',
    'action' => 'kpi'
));

Route::set('kpis2',
    'collect/id/<destination>/kpis/<cellID>/<brand>/<model>/<connType>/<connTech>/<signal>/<errorRate>/<numberOfIPs>/<route>/<mtu>/<dnsLatency>/<lac>/<timestamp>',
    array('destination' => '.+',
        'cellID' => '.+',
        'brand' => '.+', //'[0-9.]+', //'^[+-]?\d[.]?\d'
        'model' => '.+', //'[0-9.]+',
        'connType' => '.+', //'[0-9.]+',
        'connTech' => '.+',
        'signal' => '.+', //em dbm
        'errorRate' => '.+', //'[0-9.]+'
        'numberOfIPs' => '.+',
        'route' => '.+',
        'mtu' => '.+',
        'dnsLatency' => '.+',
        'lac' => '.+',
        'timestamp' => '.+'
    ))
    ->defaults(array(
    'controller' => 'collect',
    'action' => 'kpi'
));


Route::set('collect',
    'collect/id(/<destination>(/<metric>(/<dsMax>/<dsMin>/<dsAvg>/<sdMax>/<sdMin>/<sdAvg>/<timestamp>)))',
    array('destination' => '[0-9]+',
        'metric' => '[a-z_]+',
        'dsMax' => '[0-9.\-]+', //'[0-9.]+', //'^[+-]?\d[.]?\d'
        'dsMin' => '[0-9.\-]+', //'[0-9.]+',
        'dsAvg' => '[0-9.\-]+', //'[0-9.]+',
        'sdMax' => '[0-9.\-]+',
        'sdMin' => '[0-9.\-]+', //'[0-9.]+', //'.*'
        'sdAvg' => '[0-9.\-]+', //'[0-9.]+'
        'timestamp' => '[0-9.]+'
    ))
    ->defaults(array(
    'controller' => 'collect',
    'action' => 'id'
));


Route::set('default', '(<controller>(/<action>(/<id>)))')
    ->defaults(array(
    'controller' => 'welcome',
    'action' => 'index',
));