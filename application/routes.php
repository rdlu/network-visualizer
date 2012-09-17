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


Route::set('collect',
    'collect/id(/<destination>(/<metric>(/<dsMax>/<dsMin>/<dsAvg>/<sdMax>/<sdMin>/<sdAvg>/<timestamp>)))',
    array('destination' => '[0-9]+',
        'metric' => '[a-z_]+',
        'dsMax' => '.*', //'[0-9.]+', //'^[+-]?\d[.]?\d'
        'dsMin' => '.*', //'[0-9.]+',
        'dsAvg' => '.*', //'[0-9.]+',
        'sdMax' => '.*',
        'sdMin' => '.*', //'[0-9.]+', //'.*'
        'sdAvg' => '.*', //'[0-9.]+'
        'timestamp' => '.*'
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