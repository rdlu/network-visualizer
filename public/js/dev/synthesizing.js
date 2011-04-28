//to do:

//connect => OK
//template loader => ok
//template builder => ok
//ajax => ok

//em desordem de prioridade:
////fazer pegar medições das sondas via SNMP
//SYNTH.update
//gradiente da barra => OK
//evitar duplicação de seções => OK
//fazer os hooks de pop up, delete section => OK

/******************************************************************************/
//fake input para criar uma seção
/*
var sondaInfo = [
    {
     "id":1,
     "ip":"143.54.10.199",
     "nome":"nm1",
     "rtt": 0.0012,
     "loss":"15.77972",
     "tpUDP":"47.92972",
     "tpTCP": "12.12233",
     "erros":[]
    },
    {
     "id":2,
     "ip":"143.54.10.77",
     "nome":"nm2",
     "rtt": 0.0012,
     "loss":"5.972",
     "tpUDP":"7.92972",
     "tpTCP": "1.2233",
     "erros":[]
    }
];
*/
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
            console.log('~~~~~~~~~~~~do nothing~~~~~~~~~~~~~~~~~~~~');
    },
    newBoxes: function(sondaOrigemId){
        SYNTH_AJAX.getInfoSondasDestino(sondaOrigemId);
        var sondasDestino = SYNTH_AJAX.infoSondasDestino;
        //console.log("em newBoxes, sondasDestino: ", sondasDestino);
        $.each(sondasDestino, function(){
            var sonda = $(this)[0];
            var resultados = sonda.resultados;
            var limiares = sonda.limiares;
            SYNTH_TEMPLATE.buildNewBox(sondaOrigemId, resultados, limiares);
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
    },
    popupSection: function(sondaOrigemId){
        window.open('synthpopup/'+sondaOrigemId, '' , 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=800,height=600,left = 0,top = 0, modal=yes, alwaysRaised=yes');
        //window.open(URL, '" + id + "', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,width=1360,height=768,left = 0,top = 0');
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
            SYNTH.popupSection(sondaOrigemId)
        });
    },

    buildNewBox: function(sondaOrigemId, resultados, limiares){
        //html
        var template = $('#synth_template_box').clone().removeClass('template').addClass('synth_box');

        //prepara o template                                   //coloca os dados nas tags do template
        var id = resultados.id;
        var nome = resultados.nome;
        var rtt = resultados.rtt;
        var loss = resultados.loss;
        var tpTCP = resultados.tpTCP;
        var tpUDP = resultados.tpUDP;

        //console.log("em SYNTH_TEMPLATE, sondaDestId: ", id); OK
        //
            //console.log('DEBUG do template build New Box: ', template);
            template.find('.nome').text(nome);

            //console.log('esse é o campo .tpTCP: ', template.find('.tpTCP'))
            template.attr('id', 'synthBox_'+id); //coloca dinâmicamente o id único para cada sonda substituindo o id do template

            //adiciona as cores às barras
            template.find('.rtt_bar').css('background-color', SYNTH_BAR.color(rtt, limiares.rttMin, limiares.rttMax));
            template.find('.loss_bar').css('background-color', SYNTH_BAR.color(loss, limiares.lossMin, limiares.lossMax));
            template.find('.tpTCP_bar').css('background-color', SYNTH_BAR.color(tpTCP, limiares.tpTcpMin, limiares.tpTcpMax));
            template.find('.tpUDP_bar').css('background-color', SYNTH_BAR.color(tpUDP, limiares.tpUdpMin, limiares.tpUdpMax));



        //atach to secao
        //console.log('Fazer um appendTo para: ', $('#synthSecao_'+sondaOrigemId+" .synth_sondas_dest"));
        template.appendTo('#synthSecao_'+sondaOrigemId);        //CONTINUE DAQUI: refazer CSS : +".synth_sondas_dest")
        
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

    color: function(valor, limMin, limMax){
        /*
         * código de cores
         * 0 - red
         * 1 - green
         * 2 - yellow
         */
        if(valor <= limMin){return SYNTH_BAR.vermelho;}
        else if(valor >= limMax){return SYNTH_BAR.verde;}
        else {
            var media = (limMax + limMin) / 2;
            var limite;
            var r, g, b;
            var offset_r, offset_g, offset_b;
            var base_r, base_g, base_b;

            if (valor <= media){ //transition from red to yellow
                offset_r = SYNTH_BAR.amarelo_r - SYNTH_BAR.vermelho_r;
                offset_g = SYNTH_BAR.amarelo_g - SYNTH_BAR.vermelho_g;
                offset_b = SYNTH_BAR.amarelo_b - SYNTH_BAR.vermelho_b;
                limite = media;
                base_r = SYNTH_BAR.vermelho_r;
                base_g = SYNTH_BAR.vermelho_g;
                base_b = SYNTH_BAR.vermelho_b;
            }
            else { //if(valor > media) //transition from yellow to green
                offset_r = SYNTH_BAR.verde_r - SYNTH_BAR.amarelo_r;
                offset_g = SYNTH_BAR.verde_g - SYNTH_BAR.amarelo_g;
                offset_b = SYNTH_BAR.verde_b - SYNTH_BAR.amarelo_b;
                limite = limMax;
                base_r = SYNTH_BAR.amarelo_r;
                base_g = SYNTH_BAR.amarelo_g;
                base_b = SYNTH_BAR.amarelo_b;
            }
            r = base_r + Math.round(offset_r * (valor/limite));
            //console.log('r: ', r);
            g = base_g + Math.round(offset_g * (valor/limite));
            //console.log('g: ', g);
            b = base_b + Math.round(offset_b * (valor/limite));
            //console.log('b: ', b);

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

