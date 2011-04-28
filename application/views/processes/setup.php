<?php if (!$errors): ?>
<form class="bForms">
	<fieldset>
		<legend>Configuração da Sonda de Destino</legend>
		<ul class="info" id="confDest">
			<li class="loading">Configurando sonda de destino
				"<?=$destination->name . ' (' . $destination->ipaddress . ')'?>", aguarde...
			</li>
		</ul>
	</fieldset>
	<fieldset>
		<legend>Configuração da Sonda de Origem</legend>
		<ul class="info" id="confSource">
			<li class="loading">
				Configurando sonda de origem "<?=$source->name . ' (' . $source->ipaddress . ')'?>", aguarde...
			</li>
		</ul>
	</fieldset>
	<fieldset>
		<legend>Checagem final e ativação</legend>
		<ul class="info" id="confDB">
			<li class="loading">Aguardando as configurações acima serem concluídas...</li>
		</ul>
	</fieldset>
	<fieldset id="nextTasks" style="display:none;">
		<legend>Próximas tarefas</legend>
		<ul class="tasks">
			<li class="back"><a class="button p24" href="<?=url::base()?>processes/">Voltar à listagem de processos</a>
			</li>
			<li class="retry"><a class="button p24" href="<?=url::base()?>processes/new/<?=$source->ipaddress?>">Configurar
				outro processo de mesma origem</a></li>
			<li class="viewSource"><a class="button p24" href="<?=url::base()?>entities/view/<?=$source->id?>">Visualizar
				informações da entidade de origem</a></li>
			<li class="viewDestination"><a class="button p24" href="<?=url::base()?>entities/view/<?=$destination->id?>">Visualizar
				informações da entidade de destino</a></li>

		</ul>
	</fieldset>
</form>

<script type="text/javascript">
	$(function() {

		setupDestination(<?=json_encode($processIDs)?>);
		setupSource(<?=json_encode($processIDs)?>);
		var finalcount = 0;

		function setupDestination(processes) {
			$.ajax({
				url: "<?=url::site('processes/setupDestination')?>/",
				type: 'post',
				data: {'processes':processes},
				success: function(data) {
					$("#confDest").html("");
					var errStr = '';
					if(data.errors)
						jQuery.each(data.errors, function(key,value) {
							errStr += "<br />Campo \'"+key+"\' acusou: "+value;
						});
					$("<li class=" + data.class + "><strong>" + data.message+"</strong>" + errStr+"</li>").appendTo('#confDest');
					$('#confDest').removeClass('info').removeClass('errors').addClass(data.class);
					activeFinal(4);
				},
				error: function(status, msg, error) {
					console.log(error);
					$("#confDest").html("");
					$("<li class='error'>Erro interno antes do envio</li>").appendTo('#confDest');
					$('#confDest').removeClass('info').removeClass('errors').addClass('error');
					activeFinal(4);
				}
			});
		}

		function setupSource(processes) {
			$.ajax({
				url: "<?=url::site('processes/setupSource')?>/",
				type: 'post',
				data: {'processes':processes},
				success: function(data) {
					$("#confSource").html("");
					$("<li class=" + data.class + ">" + data.message + "</li>").appendTo('#confSource');
					$('#confSource').removeClass('info').removeClass('errors').addClass(data.class);
					activeFinal(5);
				},
				error: function(status, msg, error) {
					console.log(error);
					$("#confSource").html("");
					$("<li class='error'>Erro interno antes do envio</li>").appendTo('#confSource');
					$('#confSource').removeClass('info').removeClass('errors').addClass('error');
					activeFinal(5);
				}
			});
		}

		function activeFinal(num) {
			finalcount += num;
			if (finalcount > 8) {
				finalCheck(<?=json_encode($processIDs)?>);
			}
		}

		function finalCheck(processes) {
			$.ajax({
				url: "<?=url::site('processes/FinalCheck')?>/",
				type: 'post',
				data: {'processes':processes},
				success: function(data) {
					$("#confDB").html("");
					$("<li class=" + data.class + ">" + data.message + "</li>").appendTo('#confDB');
					$('#confDB').removeClass('info').removeClass('errors').addClass(data.class);
					$('#nextTasks').show('slow');
				},
				error: function(status, msg, error) {
					console.log(error);
					$("#confDB").html("");
					$("<li class='error'>Erro interno antes do envio</li>").appendTo('#confDB');
					$('#confDB').removeClass('info').removeClass('errors').addClass('error');
					$('#nextTasks').show('slow');
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
<ul class="errors">
	<?php foreach ($errors as $k => $error): ?>
	<li class="<?=$error['class']?>"><?=$error['message']?></li>
	<?php endforeach ?>
</ul>
<?php endif; ?>