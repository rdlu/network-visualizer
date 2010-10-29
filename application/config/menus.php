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
            'href'=>'synthesizing'),
        'admin'=>array(
            'title'=>__('Administrar'),
            'submenu'=>array(
                'entities'=>array(
                    'title'=>__('Sondas e Agentes'),
                    'href'=>'entities/'),
                'profiles'=>array(
                    'title'=>__('Perfis de Teste'),
                    'href'=>'profiles/')),
            'href'=>'admin'
        )
    )
);