/*
 * FALTA:
 * fazer os avisos
 * fazer funcionar o cache html
 * tirar o ip da lista da direita
 *
/

/******************************************************************************/


/************************* DEFAULTS DO MOM ************************************/
var MOM = {
    imgDir: "./",
    serverName: 'http://hrist.inf.ufrgs.br/mom/images/markers/'
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
    },
    ////Função: init
    //
    //Inicia o tamanho dos elementos HTML na página inicial
    //função utilizada para setar os valores das divs principais da tela e manter
    //a fluidez do design
    //pode ser usada com onResize também
    init: function(){
        //função abandonada por uma contrapartida em .css parecer mais webStandardista
        //var containerWidth = $(window).width();
        //containerWidth = (containerWidth < 1200)? 1200 : containerWidth;
        //alert (containerWidth);
        //var leftBarWidth = 2/3 * containerWidth; //golden rule
        //var rightBarWidth = containerWidth - leftBarWidth;
        //$('#leftBar').width(leftBarWidth);
        //$('#rightBar').width(rightBarWidth).css('marginLeft', leftBarWidth);
        //
        //var offsetMargem = DS.leftBarWdt();
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
        template.find('#sondaIp').text(ip);                                     //coloca os dados nas tags do template
        template.find('#sondaNome').text(nome);
        template.attr('id', 'sb'+id); //coloca dinâmicamente o id único para cada sonda substituindo o id do template

        //estilização
        if(status == 1) template.find('#sondaLink').addClass('sondaStatus1');     //conforme o status, adiciona uma classe diferente
        else if (status == 2) template.find('#sondaLink').addClass('sondaStatus2');
        else if (status == 3) template.find('#sondaLink').addClass('sondaStatus3');
        else template.find('#sondaLink').addClass('sondaStatus0');
        //tudo terminado, adiciona o template à página
        template.find('#sondaLink').bind('click', id, function(e){
            e.preventDefault();
            RIGHTBAR.mostraDestaque(id);
        })
        template.appendTo('#entities');                                          //coloca o template no HTML
        //alert(template.text());       
    },
    //retorna o template para a sonda que fica em destaque
    sondaDestaque: function(id, ip, nome, status, endereco, localidade){
        //alert(id);
        var alvo = $('#sondaDestaque');
        alvo.empty();
        var template = $('#sondaItemDestaque').clone().removeClass('template').addClass('sondaDestaque');
        //template.attr('id', 'sondaDestaqueBox');
        //ip
        //var ip = $('#'+id).find('sondaIp').text();
        //console.log(status);
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
}
/******************************************************************************/
//faz o parsing do XML
var Parser = {
    sondaDestaque: function(xml, num){
        return (xml.find('sonda :first'));
    }
}
/******************************************************************************/
/* Right bar: exibe a informção das sondas na barra do lado direito ***********/
/******************************************************************************/
var RIGHTBAR = {
    /* Povoa o elemento entidades do HTML com as sondas em formato reduzido */
    entitiesPovoa: function(){
        $('#entities').empty();
        $.ajax({
            type: 'get',
            url: 'retornaXML.php',
            dataType: 'xml',
            success: function(xml){
               $(xml).find("sonda").each(function(){
                   //alert("wow"); ->DEBUG(OK)
                    var sonda = $(this); //dentre as entradas do XML, escolhe cada uma das sondas
                    //pega as informações contidas no XML                    
                    var id = sonda.find('id').text();
                    var ip = sonda.find('ip').text();
                    var nome = sonda.find('nome').text();
                    var status = sonda.find('status').text();
                    //var latitude = $(this).atrr('latitude');
                    //var longitude = $(this).atrr('longitude');
                    //passa os dados para função de template que coloca os dados no HTML
                    Template.sondaItemBox(id, ip, nome, status);
           })}
    })},
    mostraDestaque: function(id){
        if( CACHED.loaded == false) CACHED.init();
        //cata esses valores do cache
        var sonda = $('#cache > entities sonda:[id=s'+id+']');
        //var sonda = entities.find()
        //console.log(sonda);
        var ip = sonda.find('ip').text();
        //var latitude = parseFloat( sonda.find('latitude').text());
        //var longitude = parseFloat( sonda.find('longitude').text());
        var nome = sonda.find('nome').text();
        var status = parseInt(sonda.find('status').text());
        var endereco;
        var localidade;
        //pega as info restantes
        $.ajax({
            type: 'get',
            url: 'retornaXML2.php',
            dataType: 'json',
            data: id,            
            async: false, //necessário, ou terá problema de sincronicidade
            success: function(dados){
                //console.log(dados.endereco);
                endereco = dados.endereco;                
                localidade = dados.localidade;
                if (dados.status != status){
                    status = dados.status;
                    SONDA.atualizaStatus(id, status); //atualiza o status no cache e nas views
                    //MAPA.atualizaStatus(id, status);
                }
                //console.log(status);
            }
        });
        //console.log(nome);
        //console.log(ip);
        //console.log(status);
        //console.log(endereco);
        //console.log(localidade);
        Template.sondaDestaque(id, ip, nome, status, endereco, localidade);
    },
    atualizaStatus: function(id, status){
        $('#sb'+id).find('#sondaLink').removeClass().addClass('sondaStatus'+status);        
    }
}
//essa variável foi criada em desacordo com o cache, para fim de agilizar a codificação
var SONDA = {};
SONDA.dadosMaps = function(id){
     $.ajax({
        type: 'get',
        url: 'getDadosMapa.php',
        dataType: 'json',
        async: true,
        success: function(dados){
           Template.sondaDestaque(dados);
        }
    })
}
SONDA.getIp = function(id){
  
}
SONDA.getStatus = function(id){

}
SONDA.getEndereco = function(id){

}
SONDA.getLocalidade = function(id){
    
}
SONDA.getMedicoes = function(id){
    
}
SONDA.getFromCache = function(id){
    var sonda = $('#cache > entities sonda:[id=s'+id+']').contents(); //CONTINUE DAQUI
    //console.log(sonda);
    return (sonda);
}
//devolve o nome da imagem que representa o estado
SONDA.statusImg = function(st){
    switch(st){
        case (0): {
            return ("cinza.png");
            break; //haha
        }
        case (1): {
            return ("verde.png");
            break; //haha
        }
        case (2): {
            return ("amarelo.png");
            break; //haha
        }
        case (3): //fallthrough
        default:
            return ("vermelho.png");
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
/***************************************************************************************/
/*************** CACHE PARA AS SONDAS **************************************************/
/***************************************************************************************/
var CACHED = {
   loaded: false   
};

CACHED.init = function(){
    
    $.ajax({
        type: 'get',
        url: 'retornaXML.php',
        dataType: 'xml',
        async: false,
        success: function(xml){
           try {
               $(xml).find("entities").appendTo("#cache");
           }
           catch(err){
               $("#cache").append( $(xml).find("entities").clone() );
           }
        }
    })
    CACHED.loaded = true;
}

var diagrama = {
    //OK
    //inicia o diagrama: canvas e etc

    //OK
    //função para desenhar as setas - optei por usar linhas curvas. A função já está feita e está no topo do arquivo por enquanto
    //EXTENDI A FN DO RAFAELJS

    //50%
    //função para desenhar as sondas, tenho que definir o layout, se vão ser pontos ou desenhos
    //devo fazer um template?
    //LAYOUT: nome e imagem. A imagem é necessária para evitar complicações desnecessárias calculando
    //ângulos e coordenadas de ponta de setas

    //70%
    //função que faz i, cache no próprio HTML das sondas que há e suas informações
 
    //0%
    //função para salvar a posição dos elementos
    //Devo fazer testes para ver se fica mais rápido com um XML dedicado ou com um bloated

    //função para povoar com os elementos dada uma posição já salva
    //devo definir outros campos no banco de dados para isso...
    //ESPAÇAMENTO : deve ser rodada apenas se não houver posição salva
    //função para espaçar os nodos "principais" - vai ser rodada apenas no início, depois o usuário arruma e salva a posição
    //função para ver se o elementos está dentro do círculo
    //função para repelir o elemento de dentro do círculo
    //função para selecionar os nodos principais. Tem que ter um critério numérico para isso
            //set up our object for dragging
        dragStart: function() {
            var g = null;
            if (!isNaN(this.idx)) {
                //find the set (if possible)
                g = groups[this.idx];
            }
            if (g) {
                var i;
                //store the starting point for each item in the set
                for(i=0; i < g.items.length; i++) {
                    g.items[i].ox = g.items[i].attr("x");
                    g.items[i].oy = g.items[i].attr("y");
                }
            }
        },
        //clean up after dragging
        dragStop: function () {
            var g = null;
            if (!isNaN(this.idx)) {
                //find the set (if possible)
                g = groups[this.idx];
            }
            if (g) {
                var i;
                //remove the starting point for each of the objects
                for(i=0; i < g.items.length; i++) {
                    delete(g.items[i].ox);
                    delete(g.items[i].oy);
                }
            }
        },
        //take care of moving the objects when dragging
        dragMove: function (dx, dy) {
            if (!isNaN(this.idx)) {
                var g = groups[this.idx];
            }

            if (g) {
                var x;
                //reposition the objects relative to their start position
                for(x = 0; x < g.items.length; x++) {
                    var obj = g.items[x];   //shorthand
                    obj.attr({x: obj.ox + dx, y: obj.oy + dy});

                    //adendo para desenhar as conexões após trocar posições
                    for (var i = connections.length; i--;) {
                        r.connection(connections[i]);
                    }
                    r.safari();
                    //optional:  We can do a check here to see what property
                    //           we should be changing.
                    // i.e. (haven't fully tested this yet):
                    // switch (obj.type) {
                    //     case "rect":
                    //     case "text":
                    //         obj.attr({ x: obj.ox + dx, y: obj.oy + dy });
                    //         break;
                    //     case "circle":
                    //         obj.attr({ cx: obj.ox + dx, cy: obj.oy + dy });
                    // }
                }
            }
        }
}
diagrama.initCanvas = function(largura, altura){
    var canvas =  Raphael(document.getElementById('diagrama'), largura, altura);
    return (canvas);
}
diagrama.construct = function (coordX, coordY) {
           this.coordX = coordX;
           this.coordY = coordY;
       }
var diagramaDnG = {
   //definição das três funções que dedinem o comportamento do drag'n'drop:
   //onmove:
       move : function (dx, dy) {
            var att = this.type == "rect" ? {x: this.ox + dx, y: this.oy + dy} : {cx: this.ox + dx, cy: this.oy + dy};
            this.attr(att);
            for (var i = connections.length; i--;) {
                r.connection(connections[i]);
            }            
            r.safari();
       },
    //onstart
       dragger : function () {
            this.ox = this.type == "rect" ? this.attr("x") : this.attr("cx");
            this.oy = this.type == "rect" ? this.attr("y") : this.attr("cy");
            this.animate({"fill-opacity": .2}, 500);
       },
    //onend
       up : function () {
            this.animate({"fill-opacity": 0}, 500);            
       }
}



var sondaDnG = {
    move : function(dx, dy) {
       var _offsetx = this.ox + dx;
       var _offsety = this.oy + dy;

       //mantém objeto dentro da área da canvas; coord X
       if (_offsetx > DS.canvasWdt - 32){
           _offsetx = DS.canvasWdt - 32;
       }
       else if (_offsetx < 0){
           _offsetx = 0;
       }
       //mantém objeto dentro da área da canvas; coord Y
       if (_offsety > DS.canvasHgt - 32){
           _offsety = DS.canvasHgt - 32; //32 é o tamenho do ícone
       }
       else if (_offsety < 0){
           _offsety = 0;
       }
       
       var att = {x: _offsetx, y: _offsety};
       this.attr(att);
       for (var i = connections.length; i--;){
           r.connection(connections[i]);
       }
       r.safari();       
    },
    dragger : function (){
       this.ox = this.attr("x");
       this.oy = this.attr("y");

    },
    up : function (dx, dy){
         
    }
}

var MAPA = {
    iconeVerde: 'markerVerde.png',
    iconeAmarelo: 'markerAmarelo.png',
    iconeVermelho: 'markerVermelho.png',
    iconeCinza: 'markerCinza.png',
    imgDir: './',
    
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
        if (CACHED.loaded == false){
            CACHED.init();
        }         
        var entities = $('#cache > entities');
        //alert (entities.text());        
        //seta os pontos
        entities.find("sonda").each(function(){
            var sonda = $(this); //dentre as entradas do XML, escolhe cada uma das sondas
            //pega as informações contidas no XML
            var id = parseInt( sonda.find('id').text());
            var ip = sonda.find('ip').text();
            var latitude = parseFloat( sonda.find('latitude').text());
            var longitude = parseFloat( sonda.find('longitude').text());
            var nome = sonda.find('nome').text();
            var status = parseInt(sonda.find('status').text());
            var iconePath = MOM.serverName + MAPA.imgDir + MAPA.statusImg(status);
            //console.log(icone);
            //alert(id);
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
            });
            //google.maps.event.addListener(MAPA.marcadores[id], 'mouseover', function(){MAPA.marcadores[id].setOptions( 'labelClass': {'opacity': 0.5}} ));
            MAPA.marcadores[id].setMap(gmap);
        });
        //seta os caminhos. Essa é a função que funciona para setar todas as polylines
        /*
        entities.find("sonda").each(function(){
            var sonda = $(this);
            var id = parseInt( sonda.find('id').text());
            
            sonda.find('agentes').find('med').each(function(){
                var med = parseInt($(this).text());
                var coord = [];
                coord.push(MAPA.myLatlng[id]);
                coord.push(MAPA.myLatlng[med]);
                //console.log(coord[0], coord[1]);
                //desenha as linhas
                var flightPath = new google.maps.Polyline({
                    path: coord,
                    map: gmap,
                    strokeColor: "#FF0000",
                    strokeOpacity: 0.8,
                    strokeWeight: 2
                });
                //flightPath.setMap(gmap);
            });
        }); */
        /******************************************************************/
        /*
        $('#cache > entities').find("sonda").each(function(){
            var flightPath = new google.maps.Polyline({
                path: MAPA.myLatlng,
                strokeColor: "#FF0000",
                strokeOpacity: 0.8,
                strokeWeight: 2
            });
            flightPath.setMap(gmap);
        }); */
     },
     desenhaLinhas: function(id, gmap){
         if(id != MAPA.ultimaLinhaDesenhada){ //só desenha se não tiver desenhado ainda
             MAPA.deletaLinhas(MAPA.ultimaLinhaDesenhada, gmap);
             var sonda = SONDA.getFromCache(id);
             console.log(sonda);
             MAPA.linhas = [];
             MAPA.ultimaLinhaDesenhada = id;
             console.log(MAPA.ultimaLinhaDesenhada);
             console.log(id);
             sonda.find('med').each(function(){
                 var med = parseInt($(this).text());
                 //console.log(med);
                 var coord = [];
                    coord.push(MAPA.myLatlng[id]);
                    coord.push(MAPA.myLatlng[med]);
                    //console.log(coord[0], coord[1]);
                    //desenha as linhas
                    //MAPA.deletaLinhas(MAPA.ultimaDesenhada, gmap);
                    //talvez deva limpar a matriz existente: corre o risco da referência das linhas ficar perdidas
                    
                    (MAPA.linhas).push( new google.maps.Polyline({
                        path: coord,
                        map: gmap,
                        strokeColor: "#EE8844", /*#FF0000*/
                        strokeOpacity: 0.8,
                        strokeWeight: 2
                    }))                                        
             })
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
     atualizaStatus: function(id, status){
         var iconePath = MOM.serverName + MAPA.imgDir + MAPA.statusImg(status);
         MAPA.marcadores[id].setIcon(iconePath);
     }
 }
MAPA.marcadores = [];
MAPA.myLatlng = [];
MAPA.linhas = [];
MAPA.ultimaLinhaDesenhada = 0;

/******************************************************************************/
/*************************** INÍCIO DO CÓDIGO *********************************/
/******************************************************************************/

$(document).ready(function(){
    //inicia os elementos na tela
    DS.init();
    CACHED.init();    
    //alert(CACHED.loaded);
/*
    diagrama.connections = []; //matriz onde serão colocadas as conexões
    diagrama.sondas = []; //matriz onde serão colocadas as sondas; serve para inicializá-las com atributos/drag'n'drop etc
    diagrama.sondaInit = function(){}; //inicializa as sondas com os argumentos e tudo
    diagrama.povoa = function(){
    //se o cache tiver ok lê do cache, do contrário inicia o cache

    //faz um loop
    };
*/
    //inicia os triggers
    $(window).resize(DS.init);
    //inicia os dados para povoar o lado direito com sondas em formato Box
    RIGHTBAR.entitiesPovoa();
    
    /*
    //inicia o diagrama
    r = diagrama.initCanvas(DS.canvasWdt, DS.canvasHgt);

       connections = [], //inicia um array com as ligações
       //inicia as formas que no futuro serão as sondas
       shapes2 = [  r.rect(190, 100, 60, 40, 2),
                   r.rect(290, 80, 60, 40, 2),
                   r.rect(290, 180, 60, 40, 2),
                   r.rect(450, 100, 60, 40, 2),
                  
                ];
        shapes = [
                        r.image("1.png",  10,  10, 32, 32),
                        r.image("2.png", 100, 100, 32, 32),
                        r.image("2.png", 400, 200, 32, 32),
                        r.image("1.png", 300, 400, 32, 32)
                  ];
       //loop que inicia as formas e seu comportamento com drag'n'drop'
       for (var i = 0, ii = shapes.length; i < ii; i++) {
            var color = Raphael.getColor();
            shapes[i].attr({/*fill: color, stroke: color, "fill-opacity": 0, "stroke-width": 2,*/ //cursor: "move"});
            //shapes[i].drag(diagramaDnG.move, diagramaDnG.dragger, diagramaDnG.up);
            /*shapes[i].drag(sondaDnG.move, sondaDnG.dragger, sondaDnG.up);
       }
       

        //Stuff to do when the page is done loading

        //The routine to repaint the drawing area
        //function redraw() {
            //workspace = Raphael('workspace', "100%", "80%");

            //Add a rectangle
            var rect = r.image("1.png",  500,  610, 32, 32).attr({cursor: "move"});
            
            var txt =  r.text(517, 650, "208.125.10.2");
            txt.attr({
                "width" : 150,
                "fill": "#fff",
                "font-size": "12pt",
                "font-weight": "bold"
            });

            //Create a set so we can move the text and rectangle at the same time
            var g = r.set(rect, txt);
            rect.idx = groups.length;   //index in our groups array,
                                        //so we can easily find the set later
            groups.push(g);

            //set up drag/drop
            // - This could be applied to the set as well, but the "dragged"
            //   object ends up being the rect anyways.
            rect.drag(dragMove, dragStart, dragStop);

       connections.push(r.connection(shapes[0], shapes[1], "#fff"));
       connections.push(r.connection(shapes[1], shapes[2], "#fff"));
       connections.push(r.connection(shapes[2], shapes[3], "#fff"));
       connections.push(r.connection(shapes[2], rect, "#fff"));
*/
/*******************************************************************************/
/* RADICAL CHANGE TO GOOGLE MAPS *******************************************/
/*******************************************************************************/
    //inicia a visu do Google maps
    MAPA.gmap = MAPA.init();
    //alert(MAPA.gmap);

    MAPA.povoa(MAPA.gmap);
//    var marcadores = [];
  //  var myLatlng = [];

    //teste: seta o ponto 0
    //MAPA.myLatlng[0] = new google.maps.LatLng(-25.363882,131.044922);
    /*
    marcadores[0] = new google.maps.Marker({
      position: myLatlng[0],
      title:"Hello World!"
    });
    marcadores[0].setMap(gmap);
    */
   //teste: seta o ponto 0 com uma label
   /*
    MAPA.marcadores[0] = new MarkerWithLabel({
       position: MAPA.myLatlng[0],
           draggable: false,
           map: gmap,
           labelContent: "255.255.255.255",
           labelAnchor: new google.maps.Point(35, 0),
           labelClass: "labels", // the CSS class for the label
           labelStyle: {opacity: 0.70},
           icon: 'http://localhost/teste/MomjavaScriptTests/computer.png',
           shadow: 'http://localhost/teste/MomjavaScriptTests/computer.png'
    }); */
   /*
    myLatlng[1] = new google.maps.LatLng(-34.397, 150.644);
    marcadores[1] = new google.maps.Marker({
      position: myLatlng[1],
      title:"Hello World!",
      icon: 'http://localhost/teste/MomjavaScriptTests/computer.png'
    });
    marcadores[1].setMap(gmap); */
    //
    /*
    var flightPath = new google.maps.Polyline({
        path: myLatlng,
        strokeColor: "#FF0000",
        strokeOpacity: 0.8,
        strokeWeight: 2
    });

  flightPath.setMap(gmap); */
});

//variables we'll need throughout the sample code
        
        //set up our object for dragging
        function dragStart() {
            var g = null;
            if (!isNaN(this.idx)) {
                //find the set (if possible)
                g = groups[this.idx];
            }
            if (g) {
                var i;
                //store the starting point for each item in the set
                for(i=0; i < g.items.length; i++) {
                    g.items[i].ox = g.items[i].attr("x");
                    g.items[i].oy = g.items[i].attr("y");
                }
            }
        }

        //clean up after dragging
        function dragStop() {
            var g = null;
            if (!isNaN(this.idx)) {
                //find the set (if possible)
                g = groups[this.idx];
            }
            if (g) {
                var i;
                //remove the starting point for each of the objects
                for(i=0; i < g.items.length; i++) {
                    delete(g.items[i].ox);
                    delete(g.items[i].oy);
                }
            }
        }

        //take care of moving the objects when dragging
        function dragMove(dx, dy) {
            if (!isNaN(this.idx)) {
                var g = groups[this.idx];
            }

            if (g) {
                var x;
                //reposition the objects relative to their start position
                for(x = 0; x < g.items.length; x++) {
                    var obj = g.items[x];   //shorthand
                    obj.attr({x: obj.ox + dx, y: obj.oy + dy});

                    //adendo para desenhar as conexões após trocar posições
                    for (var i = connections.length; i--;) {
                        r.connection(connections[i]);
                    }
                    r.safari();
                    //optional:  We can do a check here to see what property
                    //           we should be changing.
                    // i.e. (haven't fully tested this yet):
                    // switch (obj.type) {
                    //     case "rect":
                    //     case "text":
                    //         obj.attr({ x: obj.ox + dx, y: obj.oy + dy });
                    //         break;
                    //     case "circle":
                    //         obj.attr({ cx: obj.ox + dx, cy: obj.oy + dy });
                    // }
                }
            }
        }


/*********************************** OLDIES ****************************************/
//var c = canvas.circle(550,550,50).attr({fill: '#000'});
    //canvas.seta(25,25,105,450,3);
    //var d = canvas.image("1.png", 10, 10, 30, 30);
    //var e = canvas.image("2.png", 100, 100, 30, 30);

    //tentar: pega um centro, desenha uma reta em 90 graus e depois dá um rotate
  /*
    var targets = canvas.set();
    targets.push(
       canvas.circle(100, 100, 20)
    );
    targets.attr({fill: "#000", stroke: "#fff", "fill-opacity": 0});
    targets[0].click(function () {
        this.cx = this.cx || 300;
        this.animate({cx: this.cx});
        this.cx = this.cx == 300 ? 100 : 300;
    });
    */

   //inicia a canvas
       //r = Raphael("diagrama", 1800, 600),


       /*
        * var c = r.circle(300, 600, 10).attr({fill: "#000", stroke: "#fff", "fill-opacity": 0}),
    re = r.rect(350, 650, 10, 10).attr({fill: "#000", stroke: "#fff", "fill-opacity": 0});
c.animate({cx: 20, r: 20}, 2000);
re.animateWith(c, {x: 20}, 2000);
        */
