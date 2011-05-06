//to do:
//
// não deve carregar na caixa de select sondas que realizam medição porém estão 
// inativas não deve tentar fazer Zend_encode em um array com valores null.
//
////----------------
//to do:

//connect => OK
//template loader => OK
//template builder => OK
//ajax => OK

//em desordem de prioridade:
////fazer pegar medições das sondas via SNMP => via Rodrigo, Ok
//SYNTH.update
//gradiente da barra => OK
//evitar duplicação de seções => OK
//fazer os hooks de pop up, delete section => OK

/******************************************************************************/

var SYNTH_AVISOS = {
    escreve: function(numeroDoerro){
        var msgErro = getMensagem(numeroDoerro);
        $('#aviso').text(msgErro).fadeIn(2000).delay(2000).fadeOut(2000);
    },
    getMensagem: function(errNum, id){
        switch (errNum){
            case 0: //alerta se a sonda teve seu status atualizado
                var nome = SYNTH_BOX.getNome(id);
                return("O status da sonda " + nome + " foi atualizado");
            case 1: //erro de conexão ao banco de dados
                return("Não foi possível conectar ao banco de dados");
        }
    }
}

var SYNTH = {
    // faz uma nova seção na página com as medições a partir de uma origem
    newSection: function(sondaOrigemId){
        //conecta e recebe os dados das sondas destino
        //SYNTH_AJAX.getInfoSondasDestino(SondaOrigemId);
        //console.log('sondas destino em SYNTH: ', SYNTH.infoSondasDestino);
        //povoa a seção fazendo um .each newBow para cada sonda destino
        //é isso. vá tomar um café
        if( SYNTH.isOnScreen(sondaOrigemId) ){ //se não estiver na tela, coloca na tela
            //console.log('~~~~~~~~~~~~~~ Em new Section ~~~~~~~~~~~~~~');
            //console.log('SYNTH.isOnScreen(sondaOrigemId): ', SYNTH.isOnScreen(sondaOrigemId));
            (SYNTH.onScreen).push(sondaOrigemId); //e inclui aqui, já que a maldição do disabled: disabled não funciona
            SYNTH_AJAX.getInfoSondaOrigem(sondaOrigemId);
            var sondaOrigem = SYNTH_AJAX.infoSondaOrigem;
            //console.log('Em SYNTH.newSection: ', sondaOrigem.name);
            //console.log('SYNTH_AJAX.infoSondaOrigem: ', SYNTH_AJAX.infoSondaOrigem);
            var nome = sondaOrigem.name;
            var ip = sondaOrigem.ipaddress;
            var status = sondaOrigem.status;
            SYNTH_TEMPLATE.buildNewSection(sondaOrigemId, nome, ip, status);
        }
        else
            console.log('~~~~~~ Its on Screen dumb ass: do nothing');
    },
    newBoxes: function(sondaOrigemId){
        SYNTH_AJAX.getInfoSondasDestino(sondaOrigemId);
        var sondasDestino = SYNTH_AJAX.infoSondasDestino;
        //console.log("em newBoxes, sondasDestino: ", sondasDestino);
        $.each(sondasDestino, function(){
            var sonda = $(this)[0];
            console.log('$(this)[0]: ',$(this)[0]);
            var resultados = sonda.results;
            var limiares = sonda.thresholds;
            var target = sonda.target;
            console.log("sonda.target: ", sonda.target);
            SYNTH_TEMPLATE.buildNewBox(sondaOrigemId, resultados, limiares, target);
            //template.find('.synth_destino span').text(sonda.nome);
        })
    },
    hack: function(sondaOrigemId){ //coloca uma div com clear both no final, senão a altura da div fica com height: 0 e as bordas ficam ridídulas
        $('<div style="clear: both;"></div>').appendTo('#synthSecao_'+sondaOrigemId); //elementos flutuando não tem altura
    },
    isOnScreen: function(sondaOrigemId){
        //console.log('inArray retorna: ', $.inArray( String(sondaOrigemId), SYNTH.onScreen) <= -1);
        return ( $.inArray(sondaOrigemId, SYNTH.onScreen) <= -1); //inArray retorna valores <= -1 se o valor não está no array.
    },
    deleteSection: function(sondaOrigemId){
        console.log('~~~~~~~~~~~~~~~debug do delete section~~~~~~~~~~~~~~~~~')
        $('#synthSecao_'+sondaOrigemId).remove();        
        $('#synth_opt_'+sondaOrigemId).removeAttr('disabled');
        console.log($('#synth_opt_'+sondaOrigemId));
        (SYNTH.onScreen).pop(sondaOrigemId);
        console.log('SYNTH.onScreen: ', SYNTH.onScreen);
    }
    
};

SYNTH.onScreen = []; //vou precisar de um array com as sondas medidas.
                     //Não adianta ele atualizar todas as sondas, tem que ser apenas as que estão na tela
                     //Se o código não permitir que mais de uma seja aberta (com repetição), está resolvido o problema
                     //Teremos mais de uma conexão "permanente"?

var SYNTH_TEMPLATE = {
    /* pega o template que está no próprio HTML da página, completa com as informaões */
    /* trata individualmente cada uma */
    buildNewSection: function(sondaOrigemId, sondaOrigemNome, sondaOrigemIp, sondaOrigemStatus){
        //console.log('em nova seção');
        var template = $('#synth_template_secao').clone().removeClass('template').addClass('synth_secao');
        template.find('.nome').text(sondaOrigemNome);
        //console.log('sondaOrigemNome', sondaOrigemNome);
        template.attr('id', 'synthSecao_'+sondaOrigemId);
        template.appendTo('#content');

                //adiciona os eventos aos menus: exclusão e popup
        template.find('.synth_delete').bind('click', sondaOrigemId, function(e){
            e.preventDefault();            
            SYNTH.deleteSection(sondaOrigemId);
        });
        template.find('.synth_popup').bind('click', sondaOrigemId, function(e){
            e.preventDefault();
            SYNTH.popupSection(sondaOrigemId);
        });
    },

//recebe o id da sonda de origem, um objeto com os resultados, um com os limiares e um com informções da sonda de destino
//constrói o box com as informações da sonda e uma barra colorida indicando o status
    buildNewBox: function(sondaOrigemId, resultados, limiares, target){
        //html
        var template = $('#synth_template_box').clone().removeClass('template').addClass('synth_box');

        //prepara o template                                   //coloca os dados nas tags do template
        var id = target.id;
        var nome = target.name; //suporte à internacionalização entre scripts já! __("#chupafelhadapulta!")
        var rtt = resultados.rtt;
        var loss = resultados.loss;
        var tpTCP = resultados.throughputTCP;
        var tpUDP = resultados.throughput;

        //console.log("------------ Debug de SYNTH_TEMPLATE --------------");
        //console.log('id: ', id);
        //console.log('nome: ', nome);
        //console.log('rtt: ', rtt);
        //console.log('loss: ', loss);
        //console.log('tpTCP: ', tpTCP);
        //console.log('tpUDP: ', tpUDP);
        //console.log('limiares: ', limiares);

        //console.log('teste de limiares em SYBTH_TEMPLATE');
        //console.log('limiares.rtt: ', limiares.rtt);
        //console.log('limiares.rtt.min: ', limiares.rtt.min);
        //console.log('limiares.rtt.max: ', limiares.rtt.max);
        //
            //console.log('DEBUG do template build New Box: ', template);
            template.find('.nome').text(nome);

            //console.log('esse é o campo .tpTCP: ', template.find('.tpTCP'))
            template.attr('id', 'synthBox_'+id); //coloca dinâmicamente o id único para cada sonda substituindo o id do template

            //adiciona as cores às barras
            template.find('.rtt_bar').css('background-color', SYNTH_BAR.color(rtt, (limiares.rtt).min, (limiares.rtt).max, 'reversa'));
            template.find('.loss_bar').css('background-color', SYNTH_BAR.color(loss, (limiares.loss).min, (limiares.loss).max, 'reversa'));
            template.find('.tpTCP_bar').css('background-color', SYNTH_BAR.color(tpTCP, (limiares.throughputTCP).min, (limiares.throughputTCP).max, 'normal'));
            template.find('.tpUDP_bar').css('background-color', SYNTH_BAR.color(tpUDP, (limiares.throughput).min, (limiares.throughput).max, 'normal'));
            
/*
        template.bind('click', {sondaOrigemId:sondaOrigemId, id:id}, function(e){
            e.preventDefault();
            $.post("test.php", { source: sondaOrigemId, destination: id } );
            $.ajax({
              url: '/mom/synthesizing/Modal/',
              data: {
                source: sondaOrigemId,
		destination: id
              },
              success: function(htmlPage){
                  htmlPage.dialog();
              },
              dataType: 'html'
            });            
        });

        template.bind('mouseover', {sondaOrigemId:sondaOrigemId, id:id}, function(e){
            e.preventDefault();
            window.setTimout(function() {
                $.post( url, {sondaOrigemId:sondaOrigemId, id:id}, function( data ) {
                    data.tooltip();
                });
            }, 1000);//time to wait in milliseconds
        })
*/
        //prepara o html para colocar no bind abaixo
        var _html = '';
        var resultadosKeys = SYNTH_TEMPLATE.keys(resultados);
        var resultadosValues = SYNTH_TEMPLATE.values(resultados);
        for(i = 0; i < (resultados.length -1); i++){
            _html += '<span>'+resultadosKeys[i]+': '+resultadosValues[i]+'</span><br />';
        }

        template.bind('mouseover', function(e){
            e.preventDefault();
            window.setTimeout(function() {
                $(_html).tooltip();
            }, 1000);//time to wait in milliseconds
        });


        //atach to secao
        //console.log('Fazer um appendTo para: ', $('#synthSecao_'+sondaOrigemId+" .synth_sondas_dest"));
        template.appendTo('#synthSecao_'+sondaOrigemId);        //CONTINUE DAQUI: refazer CSS : +".synth_sondas_dest")
        
    },
    //do the same thing as the Perl "keys" subroutine
     keys : function  (o) {
        var accumulator = [];
        for (var propertyName in o) {
          accumulator.push(propertyName);
          }
        return accumulator;
      },

     //get values instead of keys
     values : function (o) {
        var accumulator = [];
        for (var propertyName in o) {
          accumulator.push(o[propertyName]);
          }
        return accumulator;
    }
}

var SYNTH_AJAX = {
    getInfoSondaOrigem: function (SondaOrigemId){
        $.ajax({            
           url: '/mom/synthesizing/origsondas/'+SondaOrigemId,
           type: 'get',
           dataType: 'json',
           async: false,
           cache: false,
           success: function(JSONresp){
               SYNTH_AJAX.infoSondaOrigem = JSONresp;
           }
        });
    },
    getInfoSondasDestino: function(SondaOrigemId){
        $.ajax({
            type: 'get',
            url: '/mom/synthesizing/destsondas/'+SondaOrigemId,
            dataType: 'json',
            async: false, //necessário, ou terá problema de sincronicidade
            cache: false,
            success: function(dados){
                SYNTH_AJAX.infoSondasDestino = dados;
            }
        });
    }
}

var SYNTH_BAR = { //Retorna a cor do background
    vermelho: 'rgb(206, 83, 72)',   //'#CE5348', //'#da270b', //'red',
    verde:    'rgb(119, 206, 72)',  //'#77CE48', //'#5bb333', //'green',
    amarelo:  'rgb(245, 242, 72)',  //'#D4DB4C', //'#fedb1a', //yellow

    vermelho_r: 206,
    vermelho_g: 83,
    vermelho_b: 72,

    amarelo_r: 245,
    amarelo_g: 242,
    amarelo_b: 72,

    verde_r: 119,
    verde_g: 206,
    verde_b: 72,

    color: function(valor, limMin, limMax, tipo){
        /*
         * código de cores
         * 0 - red
         * 1 - green
         * 2 - yellow
         */
        
        valor = parseFloat(valor);        
        limMin = parseFloat(limMin);        
        limMax = parseFloat(limMax);

        if(tipo == 'reversa'){ //disreverte a *)#($Q@*$)Q*$ da métrica
            var tmp = limMax;
            limMax = limMin;
            limMin = tmp;
        }

        //console.log('------------Debug da função color --------------- ');
        //console.log("valor: ", valor);
        //console.log("limMax: ", limMax);
        //console.log("limMin: ", limMin);
        //console.log("tipo: ", tipo);
/* Valores estourando o valor dos limiares */
        if(valor <= limMin){
            if(tipo == 'reversa'){
                //console.log('Metrica reversa');
                //console.log('O programa acha que valor <= limMin');
                return SYNTH_BAR.verde;
            }
            else {
                //console.log('Metrica normal');
                //console.log('O programa acha que valor <= limMin')
                return SYNTH_BAR.vermelho;
            }
        }
        else if(valor >= limMax){
            if(tipo == 'reversa'){
                //console.log('Metrica reversa');
                //console.log('O programa acha que valor >= limMax');
                return SYNTH_BAR.vermelho;
            }
            else {
                //console.log('Metrica normal');
                //console.log('O programa acha que valor >= limMax')
                return SYNTH_BAR.verde;
            }
        }
/* Valores intermediários aos limites máximo e mínimo */
        else {
            var media = (limMax + limMin) / 2;
            var limite;
            var r, g, b;
            var offset_r, offset_g, offset_b;
            var base_r, base_g, base_b;
            if(tipo == 'normal'){
                    if (valor <= media){ //transition from red to yellow
                        offset_r = SYNTH_BAR.amarelo_r - SYNTH_BAR.vermelho_r;
                        offset_g = SYNTH_BAR.amarelo_g - SYNTH_BAR.vermelho_g;
                        offset_b = SYNTH_BAR.amarelo_b - SYNTH_BAR.vermelho_b;
                        limite = media;
                        base_r = SYNTH_BAR.vermelho_r;
                        base_g = SYNTH_BAR.vermelho_g;
                        base_b = SYNTH_BAR.vermelho_b;
                        //console.log('Metrica normal');
                        //console.log('O programa acha que valor <= media');
                    }
                    else { //if(valor > media) //transition from yellow to green
                        offset_r = SYNTH_BAR.verde_r - SYNTH_BAR.amarelo_r;
                        offset_g = SYNTH_BAR.verde_g - SYNTH_BAR.amarelo_g;
                        offset_b = SYNTH_BAR.verde_b - SYNTH_BAR.amarelo_b;
                        limite = limMax;
                        base_r = SYNTH_BAR.amarelo_r;
                        base_g = SYNTH_BAR.amarelo_g;
                        base_b = SYNTH_BAR.amarelo_b;
                        //console.log('Metrica normal');
                        //console.log('O programa acha que valor > media');
                }
            }
            else{ //if (tipo == 'reversa')
                if (valor >= media){ //transition from red to yellow
                    offset_r = SYNTH_BAR.amarelo_r - SYNTH_BAR.vermelho_r;
                    offset_g = SYNTH_BAR.amarelo_g - SYNTH_BAR.vermelho_g;
                    offset_b = SYNTH_BAR.amarelo_b - SYNTH_BAR.vermelho_b;
                    limite = limMax;
                    base_r = SYNTH_BAR.vermelho_r;
                    base_g = SYNTH_BAR.vermelho_g;
                    base_b = SYNTH_BAR.vermelho_b;
                    //console.log('Metrica reversa');
                    //console.log('O programa acha que valor >= media');
                }
                else { //if(valor > media) //transition from yellow to green
                    offset_r = SYNTH_BAR.verde_r - SYNTH_BAR.amarelo_r;
                    offset_g = SYNTH_BAR.verde_g - SYNTH_BAR.amarelo_g;
                    offset_b = SYNTH_BAR.verde_b - SYNTH_BAR.amarelo_b;
                    limite = media;
                    base_r = SYNTH_BAR.amarelo_r;
                    base_g = SYNTH_BAR.amarelo_g;
                    base_b = SYNTH_BAR.amarelo_b;
                    //console.log('Metrica reversa');
                    //console.log('O programa acha que valor < media');
                }
            }
            r = base_r + Math.round(offset_r * (valor/limite));
            
            g = base_g + Math.round(offset_g * (valor/limite));
            
            b = base_b + Math.round(offset_b * (valor/limite));
            var rgb = 'rgb('+r+','+g+','+b+')';
            //console.log('rgb: ', rgb);
            
            return( 'rgb('+r+','+g+','+b+')' ); //retorna a string com um rgb com a cor
        }
    }
}

/*
var SYNTH_BAR = { //Retorna a cor do background
    vermelho: 'rgb(206, 83, 72)',//'#CE5348', //'#da270b', //'red',
    verde: 'rgb(206, 83, 72)', //'#77CE48', //'#5bb333', //'green',
    amarelo: 'rgb(206, 83, 72)', //'#D4DB4C', //'#fedb1a', //yellow
    //limiares; limites para o que é positivo e o que é negativo
    rttLimitePositivo: 10,
    rttLimiteNegativo: 5,

    lossLimitePositivo: 10,
    lossLimiteNegativo: 5,

    tpTcpLimitePositivo: 10,
    tpTcpLimiteNegativo: 5,
    
    tpUdpLimitePositivo: 10,
    tpUdpLimiteNegativo: 5,

    color: function(valor, limMin, limMax){
        if (valor >= limMin && valor <= limMax)
            return(SYNTH_BAR.amarelo);
        else if (valor < limMin)
            return(SYNTH_BAR.vermelho);
        else
            return(SYNTH_BAR.verde);
    }
}
*/
$(document).ready(function(){
    // coloca os hooks de eventos
    console.log('estou funcionando?');

    //SELECT MENU - para selecionar sonda de origem. Botão que adiciona uma seção na página
    
    $("#synth_select_add").click(function(event){
       event.preventDefault();       
       var sondaOrigemId = $('#synth_dropdown option:selected').val();
       console.log('~~~~~~~~~~~~~~No clique~~~~~~~~~~~~~~~~');
       console.log(SYNTH.onScreen);
       console.log('valor de sonda origem: ', sondaOrigemId);
       console.log("#synth_opt_"+sondaOrigemId);
       $("#synth_opt_"+sondaOrigemId).attr('disabled', 'disabled');
       if( SYNTH.isOnScreen(sondaOrigemId) ){
           SYNTH.newSection(sondaOrigemId);
           SYNTH.newBoxes(sondaOrigemId);
           SYNTH.hack(sondaOrigemId); //hack com borda (requeijão? pode ser?)
       }
    });
    //popout   
// Debugando 
//    SYNTH_TEMPLATE.buildNewSection(1, 'sonda origem', '122.326.56.58', 1); //OK
//    SYNTH_TEMPLATE.buildNewBox(1, 1, 'nome', 1, 2, 6, 20);

});

