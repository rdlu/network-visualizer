<table id="filterMenu">
	<tr>
		<td>
			<a href="<?=url::base() . Request::current()->controller()?>" class="filterMenu">Voltar</a>
		</td>
		<td>
			<i>Cadastro de Novo Processo de Medição</i>
		</td>
	</tr>
</table>
<?php if ($errors): ?>
<ul>
	<?php foreach ($errors as $error): ?>
	<li><?=$error?></li>
	<?php endforeach; ?>
</ul>
<?php endif ?>

<?= Form::open(Request::current()->controller() . '/setup/' . $sourceEntity->id, array('id' => 'newEntity', 'class' => 'bForms')) ?>
<fieldset title="Dados da Sonda de Origem">
	<legend>Dados da Sonda de Origem</legend>
	<img src="<?=url::site('images/boardMenu/source.png')?>" alt="Sonda de Origem" style="vertical-align:middle;"/>

	<div style="vertical-align:middle; display:inline-block;">
		<span class="label nice big">Sonda de Origem da Medição</span><span
			class="input nice big"><strong><?=$sourceEntity->name?> (<?=$sourceEntity->ipaddress?>)</strong></span>
	</div>
</fieldset>
<fieldset title="Dados da Sonda de Destino">
	<legend>Dados da Sonda de Destino</legend>
	<img src="<?=url::site('images/boardMenu/destination.png')?>" alt="Sonda de Destino" style="vertical-align:middle;"/>
	<span style="vertical-align:middle;">
		<label for="sonda" class="nice big"><strong>Sonda de Destino da Medição</strong></label>
		<input type="text" name="sonda" id="sonda" class="nice big" size="40">
		</span>
	<input type="hidden" name="destination" id="destination">
</fieldset>
<fieldset title="Dados do Perfil a ser utilizado">
	<legend>Métricas a serem utilizadas</legend>
	<div style="float:left;margin-right:15px; height:80px;"><img src="<?=url::site('images/boardMenu/profiles.png')?>" alt="Sonda de Destino" style="vertical-align:middle;"/>
<span class="label nice big"><strong>Métricas</strong></span></div>
	<ul class="nice" id="metricas">
		<?php foreach($metrics as $key => $metric): ?>
		<li class="nice checkbox">
			<input type="checkbox" id="metrics[<?=$key?>]" name="metrics[]" class="nice medium" value="<?=$metric->id?>" <?=($metric->order>6)?'':'checked=checked'?>>
			<label for="metrics[<?=$key?>]" class="nice little"><?=$metric->desc?> (<?=$metric->name?>)</label>
		</li>
		<?php endforeach ?>
	</ul>
</fieldset>

<fieldset title="Conjunto dos limiares a serem considerados">
	<legend>Limiares a serem utilizadas</legend>
	<div style="float:left;margin-right:15px; height:80px;"><img src="<?=url::site('images/boardMenu/profiles.png')?>" alt="Sonda de Destino" style="vertical-align:middle;"/>
<span class="label nice big"><strong>Limiares</strong></span></div>
	<div style="float:left;margin-right:15px;">
		<select name="threshold" id="threshold" class="nice big">
		<?php foreach($thresholds as $key => $threshold): ?>
			<option value="<?=$threshold->id?>" class="nice big"><?=$threshold->name?></option>
		<?php endforeach ?>
		</select>
	</div>
	<div id="limiaresInfo">
		<ul id="limiares" class="nice checkbox">
		<?php foreach(reset($thresholdsValues) as $mName => $tValues): ?>
			<li class="nice checkbox" style="padding:8px;"><?=$mName?>:
				<?php if($tValues['reverse']): ?>
					<span style="color:#336600;"><?=$tValues['max']?></span> <span style="color:#993b3b"><?=$tValues['min']?></span>
				<?php else: ?>
					<span style="color:#993b3b"><?=$tValues['min']?></span> <span style="color:#336600;"><?=$tValues['max']?></span>
				<?php endif;?>
			</li>
		<?php endforeach; ?>
		</ul>
	</div>
</fieldset>
<?= Form::submit('submit_process', 'OK', array('id'=>'submit_process','disabled' => true)) ?>
<?= Form::close() ?>

<script type="text/javascript">
	$(function() {
		var limiares = <?=json_encode($thresholdsValues)?>;

		//jQuery('#threshold').change(function(evt))

		$("#sonda").autocomplete({
			source: function(request, response) {
				$.ajax({
					url: "<?=url::site('entities/list')?>",
					type: 'post',
					data: {
						name: request.term,
						excludeId: <?=$sourceEntity->id?>
					},
					success: function(data) {
						console.log(data);
						response($.map(data.entities, function(item) {
							return {
								label: item.name+' ('+item.ipaddress+')',
								value: item.name+' ('+item.ipaddress+')',
								city: item.city,
								state: item.state,
								id: item.id
							}
						}));
					},
					error: function(status, msg, error) {
						console.error(msg);
						$("#sonda").value = '';
					}
				});
			},
			minLength: 2,
			select: function(event, ui) {
				console.log(ui.item ?
						"Selected: " + ui.item.label :
						"Nothing selected, input was " + this.value);
				jQuery('#destination')[0].value = ui.item.id;
				enableOK();
			},
			open: function() {
				$(this).removeClass("ui-corner-all").addClass("ui-corner-top");
			},
			close: function() {
				$(this).removeClass("ui-corner-top").addClass("ui-corner-all");
			}
		});

		var enableOK = function() {
			jQuery('#submit_process')[0].disabled = true;
			if(parseInt(jQuery('#destination')[0].value) > 0) {
				jQuery("#metricas input").each(function(idx,obj) {
					if(obj.checked) jQuery('#submit_process')[0].disabled = false;
				});
			}
		};

		enableOK();

		jQuery("#metricas input").bind('click',function(evt) {
			enableOK();
		});
	});
</script>