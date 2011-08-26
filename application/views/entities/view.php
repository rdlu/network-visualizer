<table id="filterMenu">
	<tr>
		<td style="text-align:left"><a href="<?=url::base()?>entities/" class="filterMenu"><img
				src="<?=url::site('images/actions/arrow_left.png')?>" alt="Adicionar nova entidade"/>&nbsp;&nbsp;&nbsp;Voltar
			à listagem</a></td>
	</tr>
</table>

<form action="#" class="bForms">
	<fieldset>
		<legend>Informações básicas</legend>
		<span class="iblock">Nome da entidade: <strong><?=$entity->name?></strong> (<?=$entity->id?>)</span>
		<span class="iblock">Endereço IP: <strong><?=$entity->ipaddress?></strong></span>
		<span class="iblock"><span class="label">Cidade: </span><strong><?=$entity->city?></strong></span>
		<span class="iblock"><span class="label">UF: </span><strong><?=$entity->state?></strong></span>
	</fieldset>
	<fieldset>
		<legend>Status</legend>
		<span class="iblock status <?=$status->getClass()?>"><?=$status->getMessage()?></span>
	</fieldset>
	<fieldset>
		<legend><strong>Papel Gerente</strong> (dispara medições contra):</legend>
		<ul>
			<?php if (count($destinations) == 0): ?>
			<li class="noresults">Esta sonda não tem o papel de gerente.</li>
			<?php else: foreach ($destinations as $destination): ?>
			<li>
				<a href="<?=Url::site('entities/view') . '/' . $destination['id']?>"><?=$destination['name']?>
					(<?=$destination['ipaddress']?>)</a>
				<img src="<?=url::base()?>images/actions/clock_delete.png" alt="Remover"
				     onclick="deleter.removeProcess(<?=$entity->id?>,'<?=$entity->name?>',<?=$destination['id']?>,'<?=$destination['name']?>')">
			</li>
			<?php endforeach; endif;  ?>
		</ul>
	</fieldset>
	<fieldset>
		<legend><strong>Papel Agente</strong> (recebe medições de):</legend>
		<ul>
			<?php if (count($sources) == 0): ?>
			<li>Esta sonda não tem o papel de agente.</li>
			<?php else: foreach ($sources as $source): ?>
			<li><a href="<?=Url::site('entities/view') . '/' . $source['id']?>"><?=$source['name']?>
				(<?=$source['ipaddress']?>)</a></li>
			<?php endforeach; endif;  ?>
		</ul>
	</fieldset>
	<fieldset>
		<legend>Avançado:</legend>
		<?php foreach ($version = $status->getVersion() as $k => $v): ?>
		<span class="iblock"><span class="label"><?= $k ?>: </span><strong><?=$v?></strong></span><br />
		<?php endforeach; ?><br /><br />
		<span class="button" id="remover">
			<img src="<?=url::base()?>images/actions/cross.png" alt="Remover">
			Remover
		</span>
	</fieldset>
</form>
<script type="text/javascript">
	var processes = <?=$procJSON?>;
	var sources = <?=Zend_Json::encode($sources)?>;
	var sourcesProcesses = <?=Zend_Json::encode($sourcesProcesses)?>;
	var destinations = <?=Zend_Json::encode($destinations)?>;
	var destinationsProcesses = <?=Zend_Json::encode($destinationsProcesses)?>;
	var myself = <?=Zend_Json::encode($entity->as_array())?>;

	var deleter = {
		html: function() {
			var destinationHTML = '';
			jQuery.each(destinations, function(idx, el) {
				destinationHTML += "<li id=\"pair-" + myself.id + '-' + el.id + "\">" + myself.name + ' -&gt; ' + el.name + '</li>';
			});

			var sourceHTML = '';
			jQuery.each(sources, function(idx, el) {
				sourceHTML += "<li id=\"pair-" + el.id + '-' + myself.id + "\">" + el.name + ' -&gt; ' + myself.name + '</li>';
			});
			return '<ul id="listaDeleter" class="lista deleter">' + destinationHTML + sourceHTML + '</ul>';
		},
		removeProcess: function (sid, src, did, dest) {
			var dialog = $('<div></div>').html('Deseja remover os processos de medição, entre ' + src + ' e ' + dest + '?').dialog({
				autoOpen: true,
				modal: true,
				minWidth: 500,
				title: 'Remover processo de medição (S:' + sid + ' D:' + did + ')',
				buttons: {
					Cancelar: function() {
						$(this).dialog("close");
					},
					OK: function() {
						jQuery.ajax({
							url: "<?=url::site('processes/remove')?>",
							type: 'post',
							data: {'source':sid,'destination':did},
							beforeSend: function() {
								dialog.html("Removendo processo, aguarde...");
								dialog.dialog("option", "buttons", {});
							},
							success: function(data) {
								if (data.errors > 0) {
									var msg = '';
									jQuery.each(data.message, function(idx, message) {
										if(idx != 0) msg += message + '<br />';
									});
									dialog.html("<b>Não foi possível remover o processo:</b><br />" + msg + "Você pode forçar a remoção, em caso das sondas já terem sido desativadas.");
									dialog.dialog("option", "buttons", {
										Cancelar: function() {
											dialog.dialog("close");
										},
										"Forçar remoção": function() {
											jQuery.ajax({
												url: "<?=url::site('processes/remove')?>",
												type: 'post',
												data: {'source':sid,'destination':did,'force':true},
												beforeSend: function() {
													dialog.html("Removendo processo em modo forçado, aguarde...");
													dialog.dialog("option", "buttons", {});
												},
												success: function(data) {
													if (data.errors > 0) {
														var msg = '';
														jQuery.each(data.message, function(idx, message) {
															if(idx != 0) msg += message + '<br />';
														});
														dialog.html("<b>Não foi possível remover o processo:</b><br />" + msg);
														dialog.dialog("option", "buttons", {
															Cancelar: function() {
																dialog.dialog("close");
															}
														});
													} else {
														dialog.html("O processo foi removido com sucesso!");
														dialog.dialog("option", "buttons", {
															OK: function() {
																window.location.reload();
															}
														});

													}
												}
											});
										}
									});
								} else {
									dialog.html("O processo foi removido com sucesso!");
									dialog.dialog("option", "buttons", {
										OK: function() {
											window.location.reload();
										}
									});
								}
							},
							error: function(status, msg, error) {
								console.log(error);
							}
						});
					}
				}
			});
		}
	};
</script>