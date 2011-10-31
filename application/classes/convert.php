<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

    class Convert {

        public static function format($value, $metrica, $formatada = false){
           $key = $metrica;
           $unidade = '';
           $decimal_place = 2;
           if($key == 'jitter' || $key == 'rtt' || $key == 'jitter' || $key == 'owd'){
                       
                       $unidade = 's';
                       $value = abs($value);
                       if($value < 1){
                           $value *= 1000; //
                           $unidade = 'ms';
                       }
                       if(false || $value < 1){
                           $value *= 1000;
                           $unidade = '&micro;s';
                       }
                       $value = round($value, $decimal_place);
           }
           elseif($key == 'pom' || $key == 'loss'){
                       $unidade = '&#37;'; //'%'
                       $value = round($value, $decimal_place);
           }
           elseif($key == 'mos'){
                       $value = round($value, $decimal_place);
           }
           elseif($key == 'throughput' || $key == 'throughput_tcp'){
                        $unidade = 'bps';
                        
                       if(true || $value > 1000){
                              $value = $value / 1000;
                                        $unidade = 'kbps';
                       }
                       if(true || $value > 1000){
                                    $value = $value / 1000;
                                    $unidade = 'mbps';
                       }
                       if(false || $value > 1000){
                                   $value = $value / 1000;
                                   $unidade = 'gpbs';
                       }
                       $value = round($value, $decimal_place);
           }         

           if($formatada){
               return($value.$unidade);
           }
           else return $value;
        }

    }



