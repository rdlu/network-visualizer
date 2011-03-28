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

Route::set('processIndex', 'processes/list(/<source>)', array('source'=>'[0-9.]+'))
    ->defaults(array(
                    'controller'=>'processes',
                    'action'=>'list'
               ));

Route::set('processNew', 'processes/new(/<source>)', array('source'=>'[0-9.]+'))
    ->defaults(array(
                    'controller'=>'processes',
                    'action'=>'new'
               ));

Route::set('processSetup', '(<controller>(/<action>/<first>/<second>/<profile>))')
	->defaults(array(
		'controller' => 'process',
		'action'     => 'setup',
	));

