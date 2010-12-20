<?php if(!isset($errors)): ?>
<form class="bForms">
    <fieldset>
        <legend>Configuração da Sonda de Destino</legend>
        <ul class="info"  id="confDest">
            <li class="loading">Configurando sonda de destino "<?=$dModel->name.' ('.$dModel->ipaddress.')'?>", aguarde...</li>
        </ul>
    </fieldset>
    <fieldset>
        <legend>Configuração da Sonda de Origem</legend>
        <ul class="info" id="confSource">
            <li class="loading">
                Aguardando as configurações acima serem concluídas...
                Configurando sonda de origem "<?=$sModel->name.' ('.$sModel->ipaddress.')'?>", aguarde...
            </li>
        </ul>
    </fieldset>
    <fieldset>
        <legend>Configurações no banco de dados</legend>
        <ul class="info" id="confDB">
            <li class="loading">Aguardando as configurações acima serem concluídas...</li>
        </ul>
    </fieldset>
</form>

<script type="text/javascript">
$(function() {

    setupDestination(<?=$destination?>,<?=$source?>,<?=$profile?>);

    function setupDestination(destination,source,profile){
         $.ajax({
		    url: "<?=url::site('processes/setupDestination')?>/"+destination+'/'+source+'/'+profile,
            type: 'get',
			success: function( data ) {
                $("#confDest").html("");
                $("<li class="+data.class+">"+data.message+"</li>").appendTo('#confDest');
                $('#confDest').removeClass('info').removeClass('errors').addClass(data.class);
                if(data.class == 'error') {
                    setupDestinationFailed();
                } else {
                    setupSource(source,destination,profile);
                }
            },
            error: function(status,msg,error) {
                console.log(error);
                $("#confDest").html("");
                $("<li class='error'>Erro interno antes do envio</li>").appendTo('#confDest');
                $('#confDest').removeClass('info').removeClass('errors').addClass('error');
                setupDestinationFailed();
            }
        });
    }

    function setupSource(source,destination,profile) {
         $.ajax({
		    url: "<?=url::site('processes/setupSource')?>/"+source+'/'+destination+'/'+profile,
            type: 'get',
			success: function( data ) {
                $("#confSource").html("");
                $("<li class="+data.class+">"+data.message+"</li>").appendTo('#confSource');
                $('#confSource').removeClass('info').removeClass('errors').addClass(data.class);
                if(data.class == 'error') {
                    setupSourceFailed();
                } else {
                    setupDB(profile,source,destination);
                }
            },
            error: function(status,msg,error) {
                console.log(error);
                $("#confSource").html("");
                $("<li class='error'>Erro interno antes do envio</li>").appendTo('#confSource');
                $('#confSource').removeClass('info').removeClass('errors').addClass('error');
                setupSourceFailed();
            }
        });
    }

    function setupDB(profile,source,destination) {
         $.ajax({
		    url: "<?=url::site('processes/SaveProfile')?>/"+profile+'/'+source+'/'+destination,
            type: 'get',
			success: function( data ) {
                $("#confSource").html("");
                $("<li class="+data.class+">"+data.message+"</li>").appendTo('#confSource');
                $('#confSource').removeClass('info').removeClass('errors').addClass(data.class);
                if(data.class == 'error') {
                    setupSourceFailed();
                } else {
                    setupDB(profile,source,destination);
                }
            },
            error: function(status,msg,error) {
                console.log(error);
                $("#confSource").html("");
                $("<li class='error'>Erro interno antes do envio</li>").appendTo('#confSource');
                $('#confSource').removeClass('info').removeClass('errors').addClass('error');
                setupSourceFailed();
            }
        });
    }

    function setupDestinationFailed() {
        $("#confSource").html("");
        $("<li class='error'>Não é possível configurar a sonda de origem, pois o pré-requisito acima falhou.</li>").appendTo('#confSource');
        $('#confSource').removeClass('info').removeClass('errors').addClass('error');
        setupSourceFailed();
    }

    function setupSourceFailed() {
        $("#confDB").html("");
        $("<li class='error'>Não é possível salvar as configurações no banco de dados, pois o pré-requisito acima falhou.</li>").appendTo('#confDB');
        $('#confDB').removeClass('info').removeClass('errors').addClass('error');
    }
});
</script>
<?php else: ?>
<?php foreach($errors as $k => $error): ?>
    <ul id="errors">
        <li class="error"><?=$error?></li>
    </ul>
<?php endforeach ?>
<?php endif; ?>