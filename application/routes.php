<?php
/**
 * Arquivo com as rotas do aplicativo
 */
 
Route::set('default', '(<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'welcome',
		'action'     => 'index',
	));


Route::set('process', '(<controller>(/<action>(/<source>/<destination>)))')
	->defaults(array(
		'controller' => 'process',
		'action'     => 'view',
	));

Route::set('processIndex', 'processes(/<source>)', array('source'=>'[0-9.]+'))
    ->defaults(array(
                    'controller'=>'processes',
                    'action'=>'index'
               ));