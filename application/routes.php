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