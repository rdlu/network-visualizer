<?php
/* 

 *
 *  * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/*
 $id = $_GET('i');

    header('Content-type: application/json');
    if ($id % 3 == 0){
        echo '{
                    "endereco": "Rua Bento Gonçalvez 9500",
                    "localidade": "RS, Porto Alegre",
                    "status": 1
              }'; //no javascript isso vira dados.campo
    }
    if ($id % 3 == 1){
         echo '{
                    "endereco": "Aleatório, xxx",
                    "localidade": "Uma cidade, um Estado",
                    "status": 2
              }'; //no javascript isso vira dados.campo
    }
    else {
        echo '{
                    "endereco": "Rua João Obino, 123",
                    "localidade": "RS, Porto Alegre",
                    "status": 3
              }'; //no javascript isso vira dados.campo
    }
?>*/

    //header('Cache-Control: no-cache, must-revalidate');
    //header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); //custo alto
    header('Content-type: application/json');
    echo '{
                "endereco": "Rua Bento Gonçalvez 9500",
                "localidade": "RS, Porto Alegre",
                "status": 1
          }'; //no javascript isso vira dados.campo
?>