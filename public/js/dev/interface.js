/******************************************************************************/
/************************* DEFAULTS DO MOM ************************************/

var perfect = [
{
    "id":29,
    "ip":"0c298afe36.vivo.com.br",
    "nome":"sc-nm-ub-ag-1",
    "status":1,
    "latitude":"-13.516666",
    "longitude":"-42.016666",
    "agentes":[],
    "gerentes":[26]
},{
    "id":30,
    "ip":"0c295e1626.vivo.com.br",
    "nome":"sp-nm-ub-ag-2",
    "status":1,
    "latitude":"-22.166666",
    "longitude":"-47.433333",
    "agentes":[],
    "gerentes":[26]
    },{
    "id":26,
    "ip":"143.54.10.94",
    "nome":"rj-nm-ub-man-1",
    "status":1,
    "latitude":"-22.902777",
    "longitude":"-43.2075",
    "agentes":[29,30],
    "gerentes":[]
}];

var MOM = {
    imgDir: "../mom/images/markers/",
    serverName: 'http://fringe.inf.ufrgs.br/',
    //script1: '../mom/welcome/infoMapa', -> função idêntica à infoMapaJ, mas retorna XML
    script2: 'retornaXML2.php',
    //script_info_mapa: '../mom/welcome/infoMapa',
    script_info_bar: '../mom/welcome/infoBar',
    info_mapa_json: '../mom/welcome/infoMapaJ',
    infoMedicoesSondaOrigem: '../mom/synthesizing/destsondas/'
}
//namespace reservado para o layout e os resizes
var DS = { //display screen :)
    _self: $(this),
    canvasWdt: 1000,
    canvasHgt: 800,
    rightBarWdt: function(){
        return ($('#rightBar').width());
    },
    rightBarHgt: function(){
        return ($('#rightBar').height());
    },
    leftBarWdt: function(){
        return ($('#leftBar').width());
    },
    leftBarHgt: function(){
        return ($('#leftBar').height());
    }
}
/******************************************************************************/
var AVISOS = {
    escreve: function(numeroDoerro){
        var msgErro = getMensagem(numeroDoerro);
        $('#aviso').text(msgErro).fadeIn(2000).delay(2000).fadeOut(2000);
    },
    getMensagem: function(errNum, id){
        switch (errNum){
            case 0: //alerta se a sonda teve seu status atualizado
                var nome = SONDA.getNome(id);
                return("O status da sonda " + nome + " foi atualizado");
            case 1:
                return("Não foi possível conectar ao banco de dados");
        }
    }
}

/******************************************************************************/
//namespace responsãvel para pegar os templates incluídos dentro do HTML e
// combinar com as informações dadas
var Template = {
    //função que faz as caixas com as sondas na barra da direita
    sondaItemBox: function(id, ip, nome, status){
        //html        
        var st = MOM.imgDir + SONDA.statusImg(status); //concatena as strings para o nome da figura do estado da sonda
        var template = $('#sondaItemBox').clone().removeClass('template').addClass('sondaNd');        //clona o template que está no HTML
        //template.find('#sondaIp').text(ip);                                     //coloca os dados nas tags do template
        template.find('#sondaNome').text(nome);
        template.attr('id', 'sb'+id); //coloca dinâmicamente o id único para cada sonda substituindo o id do template

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
        template.appendTo('#entities');                                          //coloca o template no HTML
        //alert(template.text());
        },
        //retorna o template para a sonda que fica em destaque
        sondaDestaque: function(id, ip, nome, status, endereco, localidade){
        //alert(id);
        var alvo = $('#sondaDestaque');
        alvo.empty(); //limpa a div
        var template = $('#sondaItemDestaque').clone().removeClass('template').addClass('sondaDestaque');
        //var ip = $('#'+id).find('sondaIp').text();
        
        var st = MOM.imgDir + SONDA.statusImg(status); //concatena as strings para o nome da figura do estado da sonda
        
        template.find('#sondaNome').text(nome);
        template.find('#sondaIp').text(ip);
        template.find('#sondaLocalidade').text(localidade);
        template.find('#sondaEndereco').text(endereco);
        template.find('.sondaStatusImg').attr({
            src: st,
            alt: status
            });
        template.appendTo('#sondaDestaque'); 
        }
        };

        /******************************************************************************/
        /* Right bar: exibe a informção das sondas na barra do lado direito ***********/
        /******************************************************************************/
        var RIGHTBAR = {
        /* Povoa o elemento entidades do HTML com as sondas em formato reduzido */
        entitiesPovoa: function(){
        $('#entities').empty();
        var sondas = CACHED.JSONresponse;
        $.each(sondas, function(){            
            var sonda = $(this)[0];
            var id = sonda.id;
            var ip = sonda.ip;
            var nome = sonda.nome;
            var status = sonda.status;

            Template.sondaItemBox(id, ip, nome, status);
            });
        },
        mostraDestaque: function(id){
        if( CACHED.loaded == false) CACHED.infoMapaJ();
        //cata esses valores do cache
        var sonda = SONDA.getFromCache(id);
        var nome = sonda.nome;
        var status = sonda.status;
        var ip = sonda.ip;
        /* cache HTML
        var sonda = $('#cache > entities sonda:[id=s'+id+']');
        //var sonda = entities.find()        
        var ip = sonda.find('ip').text();
        //var latitude = parseFloat( sonda.find('latitude').text());
        //var longitude = parseFloat( sonda.find('longitude').text());
        var nome = sonda.find('nome').text();
        var status = parseInt(sonda.find('status').text());
        */

        var endereco;
        var localidade;
        //pega as info restantes
        //prepara o objeto json para passar
                
        $.ajax({
            type: 'get',
            url: MOM.script_info_bar+'/'+id,
            dataType: 'json',
            async: false, //necessário, ou terá problema de sincronicidade
            cache: false,
            success: function(dados){
            endereco = dados.endereco;
            localidade = dados.localidade;
            status = parseInt(status);
            if (dados.status != status){
            status = dados.status;
            //SONDA.atualizaStatus(id, status); //atualiza o status no cache e nas views
            //MAPA.atualizaStatus(id, status);
            }
            }
            });
        
        Template.sondaDestaque(id, ip, nome, status, endereco, localidade);
        },
        atualizaStatus: function(id, status){
        $('#sb'+id).find('#sblink'+id).removeClass().addClass('sondaLink').addClass('sondaStatus'+status);
        },
        clicked: function(id){
        $('#sblink' + SONDA.lastClicked).removeClass('sondaBgClicked').addClass('sondaBg');
        $('#sblink' + id).removeClass('sondaBg').addClass('sondaBgClicked');
        }
        }
        //essa variável foi criada em desacordo com o cache, para fim de agilizar a codificação
        var SONDA = {
        clicked: function(id){
        if(SONDA.lastClicked != id){
        RIGHTBAR.clicked(id);
        MAPA.clicked(id);

        SONDA.lastClicked = id;
        console.log('dentro de SONDA.clicked: ', SONDA.lastClicked);
        }
        }
        };
        /* obsoleta
SONDA.dadosMaps = function(){
     $.ajax({
        type: 'get',
        url: MOM.script_info_mapa,
        dataType: 'xml',
        async: false,
        success: function(dados){           
           return(dados);
        }
    })
}
*/
        SONDA.getStatus = function(id){
        sonda = SONDA.getFromCache(id);
        return (sonda.status);
        }
        SONDA.getFromCache = function(id){
        var sondas = CACHED.JSONresponse;
        var found = null;
        $.each(sondas, function(){
            var sonda = $(this)[0];
            if (sonda.id == id){
            found = sonda;
            }
            });
        /* código antigo que usava o cache xml
    var sonda = $('sonda:[id=s'+id+']'); //CONTINUE DAQUI -> Isso não está mais funcionando...'sonda:[id=s'+id+']'
        */
        return (found);
        }
        //devolve o nome da imagem que representa o estado
        SONDA.statusImg = function(st){
        switch(st){
        case (0): {
        return ("icon_cinza.png");
        break;
        }
        case (1): {
        return ("icon_verde.png");
        break;
        }
        case (2): {
        return ("icon_amarelo.png");
        break;
        }
        case (3):
        return ("icon_vermelho.png");
        break;
        }
        }

        /* Atualiza o status no cache e troca os ícones nos locais correspondentes */
        SONDA.atualizaStatus = function(id, status){ //atualiza o status no
        var sonda = $('#cache > entities sonda:[id=s'+id+']');
        //atualiza cache
        sonda.find('status').text(status);
        //atualiza barra da direita
        RIGHTBAR.atualizaStatus(id, status);
        //atualiza mapa
        MAPA.atualizaStatus(id, status);
        //se tiver mais alguma outra view, deve ter outras funções que atualizem o status
        }
        SONDA.lastClicked = null;

        /***************************************************************************************/
        /*************** CACHE PARA AS SONDAS **************************************************/
        /***************************************************************************************/

        // A variável CACHE.JSONresponse armazena os dados das sondas.
        // Para inicializá-la, use CACHED.infoMapaJ

        //Para pegar informações de uma sonda específica, use SONDA.getFromCache(id); onde id é o id da sonda

        var CACHED = {
        //loaded: false,
        infoMedicoes: function(sondaOrigemId){
        $.ajax({
            type: 'get',
            url: '../mom/synthesizing/destsondas/'+sondaOrigemId,
            dataType: 'json',
            async: false, //necessário, ou terá problema de sincronicidade
            cache: false,
            success: function(medicoes){
            CACHED.medicoes = medicoes;
            //CACHED.last = true;
            }
            });
        },
        infoMapaJ: function(){
        $.ajax({
            url: MOM.info_mapa_json,
            type: 'get',
            dataType: 'json',
            async: false,
            success: function(JSONresp){
            CACHED.JSONresponse = JSONresp;
            CACHED.loaded = true;
            console.log('todas info das sondas', JSONresp);
            }
            })
        },
        infoMedicoesSondaOrigem: function(sondaOrigemId){
        $.ajax({
            url: MOM.infoMedicoesSondaOrigem+sondaOrigemId,
            type: 'get',
            dataType: 'json',
            async: false,
            success: function(data){
            CACHED.medicoes = data;
            }
            })
        }
        };

        var MAPA = {
        iconeVerde: 'verdeNormal.png', //markerGimp.png',
        iconeAmarelo: 'amareloNormal2.png', //markerAmarelo.png',
        iconeVermelho: 'vermelhoNormal.png',//'markerVermelho.png',
        iconeCinza: 'cinzaNormal.png',
        iconeVerdeClicked: 'verdeSelecionado.png', //markerVerdeV2.png',
        iconeAmareloClicked: 'amareloSelecionado2.png', //markerAmareloClicked.png',
        iconeVermelhoClicked: 'vermelhoSelecionado.png',//'markerVermelho.png',
        iconeCinzaClicked: 'cinzaSelecionado.png', //'markerCinza.png',
        
        init: function(){
        //inicia o mapa
        var latlng = new google.maps.LatLng(-21.698265, -46.757812);
        var opcoes = {
        zoom: 4,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var gmap = new google.maps.Map(document.getElementById("mapa"), opcoes);
        return (gmap);
        },
        //pega os pontos do cache e desenha no mapa
        povoa: function(gmap){
        var sondas = CACHED.JSONresponse;
            
        $.each(sondas, function(){            
            var sonda = $(this)[0];
            var id = sonda.id;
            var ip = sonda.ip;
            var latitude = sonda.latitude;
            var longitude = sonda.longitude;
            var nome = sonda.nome;
            var status = sonda.status;
            var iconePath = MOM.imgDir + MAPA.statusImg(status);
            
            MAPA.myLatlng[id] = new google.maps.LatLng(latitude, longitude);
            MAPA.marcadores[id] = new google.maps.Marker({
                position: MAPA.myLatlng[id],
                draggable: false,
                //labelContent: nome,
                //labelAnchor: new google.maps.Point(35, 0),
                //labelClass: "labels", // the CSS class for the label
                //labelStyle: {opacity: 0},
                title: nome,
                icon: iconePath,
                shadow: iconePath
                });

            //MAPA.marcadores[id].setMap(gmap);
            google.maps.event.addListener(MAPA.marcadores[id], 'click', function(){
                RIGHTBAR.mostraDestaque(id);
                MAPA.desenhaLinhas(id, gmap);
                SONDA.clicked(id); //efeitos para visualização do clique
                });
            //google.maps.event.addListener(MAPA.marcadores[id], 'mouseover', function(){MAPA.marcadores[id].setOptions( 'labelClass': {'opacity': 0.5}} ));
            MAPA.marcadores[id].setMap(gmap);
            })
        },
        desenhaLinhas: function(id, gmap){
         
        if(id != MAPA.ultimaLinhaDesenhada){ //só desenha se não tiver desenhado ainda
        MAPA.deletaLinhas(MAPA.ultimaLinhaDesenhada, gmap);
        var sonda = SONDA.getFromCache(id);
        console.log(sonda);
        MAPA.linhas = [];
        MAPA.ultimaLinhaDesenhada = id;
        //pega os valores das medições
        CACHED.infoMedicoesSondaOrigem(id);
        var medicoes = CACHED.medicoes;
        console.log("------------------------------");
        console.log("medicoes: ", medicoes);
        //para pegar o objeto certo que contém as medições terei que entrar em cada um deles, verificar o medicoes.target.id,
        //e, se for o correto, retornar o medicoes.results
        console.log("id da sonda de origem: ", id);
        console.log("------------------------------");
        //itera sobre as medições dos agentes e gerentes

        $.each(sonda.agentes, function(key, value){
            var med = value;
            var coord = [];
            coord.push(MAPA.myLatlng[id]);
            coord.push(MAPA.myLatlng[med]);
            //desenha as linhas
            //MAPA.deletaLinhas(MAPA.ultimaDesenhada, gmap);
            //talvez deva limpar a matriz existente: corre o risco da referência das linhas ficar perdidas
            var linha = new google.maps.Polyline({
                path: coord,
                map: gmap,
                strokeColor: "#EE8844", /*#FF0000*/
                strokeOpacity: 0.8,
                strokeWeight: 3
                });
            // TO_DO : função que pegue 1 JSON com as medições do agente
            // CUIDAR : tem duas funções: uma que cuida das linhas claras e outra das linhas escuras.]
            //          isso é estúpido, refatorar
            google.maps.event.addListener(linha, 'mouseover', function(){
                //alert("mOUs3 Ouv4h1!1");
                // TO_DO: adicionar infoView aqui
                //code sample:
                /*
                        coordInfoWindow = new google.maps.InfoWindow({content: "Chicago, IL"});
                        coordInfoWindow.setContent(latlngStr + worldCoordStr + pixelCoordStr + tileCoordStr);
                        coordInfoWindow.setPosition(chicago); //seta o ponto
                        coordInfoWindow.open(map);
                        */
                });
            (MAPA.linhas).push(linha);
            });
        $.each(sonda.gerentes, function(key, value){
            var med = value;
            var coord = [];
            coord.push(MAPA.myLatlng[id]);
            coord.push(MAPA.myLatlng[med]);
            //desenha as linhas
            //MAPA.deletaLinhas(MAPA.ultimaDesenhada, gmap);
            //talvez deva limpar a matriz existente: corre o risco da referência das linhas ficar perdidas
            var linha = new google.maps.Polyline({
                path: coord,
                map: gmap,
                strokeColor: "#EE9955",
                strokeOpacity: 0.6,
                strokeWeight: 2
                });
            google.maps.event.addListener(linha, 'mouseover', function(){
                //alert("mOUs3 Ouv4h1!1");
                });
            (MAPA.linhas).push(linha);
            });
        }
        },
        deletaLinhas: function(){
        $.each(MAPA.linhas, function(i, k){
            MAPA.linhas[i].setMap(null);
            })
        },
        /* retorna uma string com o nome da imagem que representa o status */
        statusImg: function(status){
        switch(status){
        case (0): {
        return (MAPA.iconeCinza);
        break; //haha
        }
        case (1): {
        return (MAPA.iconeVerde);
        break; //haha
        }
        case (2): {
        return (MAPA.iconeAmarelo);
        break; //haha
        }
        default:
        case (3): { //fallthrough
        return (MAPA.iconeVermelho);
        break;
        }
        }
        },
        statusImgClicked: function(status){
        switch(status){
        case (0): {
        return (MAPA.iconeCinzaClicked);
        break; //haha
        }
        case (1): {
        return (MAPA.iconeVerdeClicked);
        break; //haha
        }
        case (2): {
        return (MAPA.iconeAmareloClicked);
        break; //haha
        }
        default:
        case (3): { //fallthrough
        return (MAPA.iconeVermelhoClicked);
        break;
        }
        }
        },
        atualizaStatus: function(id, status){
        var iconePath = MOM.imgDir + MAPA.statusImg(status);
        MAPA.marcadores[id].setIcon(iconePath);
        },
        desenhaTodasLinhas: function(){
        if (MAPA.todasLinhasDesenhadas == false){
        var sondas = CACHED.JSONresponse;
        $.each(sondas, function(){
            var id = sonda.id;
            MAPA.desenhaLinhas(id, gmap);
            });
        }
        },
        clicked: function(id){
        //atualiza o novo
        var status = SONDA.getStatus(id);
        console.log('status', status);
        console.log('dentro de MAPA: ', SONDA.lastClicked);
        var iconePath = MOM.imgDir + MAPA.statusImgClicked(status);
         
        MAPA.marcadores[id].setIcon(iconePath);
         
        //retorna o último clicado ao normal
        if(SONDA.lastClicked != null){
        status = SONDA.getStatus(SONDA.lastClicked);
        iconePath = MOM.imgDir + MAPA.statusImg(status);
        MAPA.marcadores[SONDA.lastClicked].setIcon(iconePath);
        }
        }
        }
        MAPA.marcadores = [];
        MAPA.myLatlng = []; //não deixar o código com 400 linhas. Wirlau diz que é o número da morte
        MAPA.linhas = [];
        MAPA.ultimaLinhaDesenhada = null;
        MAPA.todasLinhasDesenhadas = false;
        /******************************************************************************/
        /*************************** INÍCIO DO MAIN ***********************************/
        /******************************************************************************/

        $(document).ready(function(){
            //inicia os elementos na tela
            //CACHED.init();
            CACHED.infoMapaJ();
            //inicia os dados para povoar o lado direito com sondas em formato Box
            console.log("CACHED.JSONresponse: ", CACHED.JSONresponse);
            RIGHTBAR.entitiesPovoa();
            MAPA.gmap = MAPA.init();
            MAPA.povoa(MAPA.gmap);
        });