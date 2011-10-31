<?php defined('SYSPATH') or die('No direct script access.');

return array(

	'main' => array(
        'index'=>array(
            'title'=>__('Início'),
            'href'=>''),
        'reports'=>array(
            'title'=>__('Relatórios'),
            'href'=>'reports'),
        'synthesizing'=>array(
            'title'=>__('Sintetização'),
            'href'=>'dashboard'),
        'admin'=>array(
            'title'=>__('Administrar'),
            'submenu'=>array(
	       'processes'=>array(
                    'title'=>__('Processos de Medição'),
                    'href'=>'processes/'),
               'entities'=>array(
                    'title'=>__('Sondas e Agentes'),
                    'href'=>'entities/'),
               'profiles'=>array(
                    'title'=>__('Métricas e Perfis de Teste'),
                    'href'=>'profiles/'),
	        //'logviewer'=>array(
		//            'title'=>__('Registro de Eventos (Log)'),
		//            'href'=>'log/'),
                'account'=>array(
                    'title'=>__('Controle de Usuário'),
                    'href'=>'account/'
                ),
                'winagent'=>array(
                    'title'=>__('Agente Windows'),
                    'href'=>'winagent/'
                ),
	            ),
            'href'=>'admin'
        )
    )
);