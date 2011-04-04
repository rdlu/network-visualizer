//to do:

//connect

//template loader

//template builder

//ajax

//SONDA: update

/******************************************************************************/
//fake input para criar uma seção
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
    newSection: function(idSondaOrigem){
        //conecta e recebe os dados das sondas destino
        var sondasDestino = SYNTH_AJAX.getInfoSondasDestino(idSondaOrigem);
        console.log(sondasDestino);
        //povoa a seção fazendo um .each newBow para cada sonda destino

        //é isso. vá tomar um café

    }
};

var SYNTH_AJAX = {
    getInfoSondasDestino: function(idSondaOrigem){
        $.ajax({
            type: 'get',
            url: '../mom/welcome/infoBar/'+idSondaOrigem,
            dataType: 'json',
            async: false, //necessário, ou terá problema de sincronicidade
            cache: false,
            success: function(dados){
                dados = sondaInfo;
                return (dados);
            }
        });
    }
}

var SYNTH_TEMPLATE = {
    /* pega o template que está no próprio HTML da página, completa com as informaões */
    /* trata individualmente cada uma */
    buildNewSection: function(){

    },
    //
    buildNewBox: function(secaoid, id, nome, rtt, loss, tpUDP, tpTCP){
        //html        
        var template = $('#synthBox').clone().removeClass('template');
        //prepara o template                                   //coloca os dados nas tags do template
        template.find('.synth_destino span').text(nome);
        template.find('.rtt span').text(rtt);
        template.find('.loss span').text(loss);
        template.find('.tpUDP span').text(tpUDP);
        template.find('.tpTCP span').text(tpTCP);
        template.attr('id', 'synthBox'+id); //coloca dinâmicamente o id único para cada sonda substituindo o id do template

        //prepara o link de pop-up

        //estilização
        var link = template.find('#sondaLink');
        if(status == 1) link.addClass('sondaStatus1');     //conforme o status, adiciona uma classe diferente
        else if (status == 2) link.addClass('sondaStatus2');
        else if (status == 3) link.addClass('sondaStatus3');
        else link.addClass('sondaStatus0');
        //terminado, adiciona o template à página
        link.bind('click', id, function(e){
            e.preventDefault();
            RIGHTBAR.mostraDestaque(id);
            MAPA.desenhaLinhas(id, MAPA.gmap);
            SONDA.clicked(id);
        });
        link.attr('id', 'sblink'+id).addClass('sondaLink').addClass('sondaBg');
        template.appendTo('#secao'+secaoid);                                          //coloca o template no HTML
        //alert(template.text());
    }
}

SYNTH_BAR = {
    verde: 1,
    vermelho: 0,
    amarelo: 2,
    limiat: function(métrica, valor){
        /*
         * código de cores
         * 0 - red
         * 1 - green
         * 2 - yellow
         */
        switch(metrica){
            case 'rtt':
                return(SYNTH_BAR.verde);
                break;
            case 'loss':
                return(SYNTH_BAR.verde);
                break
            case 'tptcp':
                return(SYNTH_BAR.verde);
                break;
            case 'tpudp':
                return(SYNTH_BAR.verde);
                break;
        }
    }
}

$(document).ready(function(){
    // coloca os hooks de eventos

    //SELECT MENU - para selecionar sonda de origem. Botão que adiciona uma seção na página
    $("#synth_select_add").click(function(event){
       event.preventDefault();       
       var sondaOrigemId = $('#synth_dropdown').val();
       $("#synth_opt_"+sondaOrigemId).attr({'disabled': 'disabled'});       
       SYNTH.newSection(sondaOrigemId);
    });

    //popout
});