<table id="filterMenu">
	<tr>
		<td>
			<a href="<?=url::base() . Request::current()->controller()?>" class="filterMenu">Voltar</a>
		</td>
		<td>
			<i>Cadastro de Nova Entidade</i>
		</td>
	</tr>
</table>
<?php if ($success): ?>
<ul class="info">
	<li class="success">Entidade registrada com sucesso!</li>
	<li class="info"><a href="<?=url::base() . Request::current()->controller?>">Voltar para a listagem de entidades.</a>
	</li>
</ul>
<?php endif ?>
<?php if ($errors): ?>
<ul class="errors">
	<?php foreach ($errors as $error): ?>
	<li class="error"><?=$error?></li>
	<?php endforeach; ?>
</ul>
<?php endif ?>

<?= Form::open(Request::current()->controller() . '/' . Request::current()->action() . '/' . Request::current()->param('id', 0), array('id' => 'newEntity', 'class' => 'bForms')) ?>
<fieldset title="Dados Obrigatórios">
	<legend>Dados Obrigatórios</legend>
    <?=$entity->input('isAndroid', array('id' => 'isAndroid'));?><br/>
    <?=$entity->label('ipaddress');?>
	<?=$entity->input('ipaddress', array('id' => 'ipaddress'));?>
	<span id="ipCheck" class="input info">Endereços válidos: número IPv4 (123.123.123.123) ou hostname netmetric (5188888888.vivo.com.br)</span><br/>
	<?=$entity->label('city');?>
	<?=$entity->input('city', array('id' => 'city', $disabled => $disabled));?>
	<?=$entity->label('state');?>
	<?=$entity->input('state', array('id' => 'state', $disabled => $disabled));?><br/>
	<?=$entity->label('name');?>
	<?=$entity->input('name', array('id' => 'name', $disabled => $disabled));?><br/>

</fieldset>
<fieldset title="Dados Obrigatórios">
	<legend>Dados Opcionais</legend>
	<?=$entity->label('district');?>
	<?=$entity->input('district', array('id' => 'district', $disabled => $disabled));?>
	<?=$entity->label('address');?>
	<?=$entity->input('address', array('id' => 'address', $disabled => $disabled));?>
	<?=$entity->label('addressnum');?>
	<?=$entity->input('addressnum', array('id' => 'addressnum', $disabled => $disabled));?><br/>
	<?=$entity->label('latitude');?>
	<?=$entity->input('latitude', array('id' => 'latitude', $disabled => $disabled));?>
	<?=$entity->label('longitude');?>
	<?=$entity->input('longitude', array('id' => 'longitude', $disabled => $disabled));?><br/>
	<?=$entity->label('description', array('class' => 'textarea'));?>
	<?=$entity->input('description', array('id' => 'description', $disabled => $disabled));?>

</fieldset>

<?= Form::submit('submit_entity', 'Salvar') ?>
<?= Form::close() ?>
<script type="text/javascript">
	$(function() {
		//$('input#ipaddress').focus();
		function checkIp() {
			$.ajax({
				type: "POST",
				url: "<?=url::base() . 'tools/check/'?>",
				data: "ip=" + $('input[name$="ipaddress"]').val(),
				dataType: 'json',
				beforeSend: function(req) {
					$('span#ipCheck').remove();
					$('input[name$="ipaddress"]').after('<span id="ipCheck" class="input wait">Checando endereço via SNMP, aguarde...</span>');
					disableFields();
				},
				success: function(data) {
					$('span#ipCheck').remove();
					console.log(data);
					if (data.error) {
						$('input[name$="ipaddress"]').after('<span id="ipCheck" class="input error">'+data.error+'</span>');
						disableFields();
					} else if (data.data.version) {
						$('input[name$="ipaddress"]').after('<span id="ipCheck" class="input sucess">Host contactado com sucesso. Info: Netmetric ' + data.data.nmVersion + ' LibGparc ' + data.data.libVersion + ' Modem ' + data.data.modemInfo + '</span>');
						enableFields();
					} else {
						$('input[name$="ipaddress"]').after('<span id="ipCheck" class="input error">Não houve resposta do host no IP indicado. Cheque se a instalação foi feita corretamente.</span>');
						disableFields();
					}
				},
				error: function(status, msg, error) {
					$('span#ipCheck').remove();
					disableFields();
					$('input[name$="ipaddress"]').after('<span id="ipCheck" class="input error">Checagem de IP falhou. Verifique se o IP digitado é válido.</span>');
				}
			});
		}

		$('input[name$="ipaddress"]').blur(function() {
			if(!$('#isAndroid').is(':checked')) {
                checkIp();
            } else {
                if($('input#ipaddress').val().match(/vivo.com.br/) == null)
                    $('input#ipaddress').val($('input#ipaddress').val()+'.vivo.com.br');
            }

		});

        $('#isAndroid').click(function (evt) {
            if($('#isAndroid').is(':checked')) {
                enableFields();
                $('span#ipCheck').remove();
                $('input[name$="ipaddress"]').after('<span id="ipCheck" class="input info">A verificação do agente Android não é automática, verifique se o nome está correto antes de prosseguir.</span>');
                $("label[for='ipaddress']").text("Nome do agente");

                $('input#ipaddress').focus();
            } else {
                $('span#ipCheck').remove();
                disableFields();
                $("label[for='ipaddress']").text("Endereço IP");
                $('input#ipaddress').focus();
            }
        });

		function enableFields() {
			$('#newEntity input').each(function(index, obj) {
				obj.disabled = false;
			});

			$('#newEntity select').each(function(index, obj) {
				obj.disabled = false;
			});

			$('#newEntity textarea')[0].disabled = false;
			$('#newEntity input#city')[0].focus();
		}

		function disableFields() {
			$('#newEntity input').each(function(index, obj) {
				obj.disabled = true;
			});

			$('#newEntity select').each(function(index, obj) {
				obj.disabled = true;
			});

			$('#newEntity textarea')[0].disabled = true;
			$('#newEntity input#ipaddress')[0].disabled = false;
            $('#isAndroid')[0].disabled = false;
		}


		function log(message) {
			console.info(message);
		}

		$("#city").autocomplete({
			source: function(request, response) {
				$.ajax({
					url: "http://ws.geonames.org/searchJSON",
					dataType: "jsonp",
					data: {
						featureClass: "P",
						style: "medium",
						maxRows: 5,
						name_startsWith: request.term,
						country: 'br'
					},
					success: function(data) {
						response($.map(data.geonames, function(item) {
							return {
								label: item.name + (item.adminName1 ? ", " + item.adminName1 : ""),
								value: item.name,
								state: item.adminName1,
								longitude: item.lng,
								latitude: item.lat

							}
						}));
					}
				});
			},
			minLength: 2,
			select: function(event, ui) {
				log(ui.item ?
						"Selected: " + ui.item.label :
						"Nothing selected, input was " + this.value);
				var state = $('option:contains("' + ui.item.state + '")').val()
				$('#state').val(state);
				entName(ui.item, state);

			},
			open: function() {
				$(this).removeClass("ui-corner-all").addClass("ui-corner-top");
			},
			close: function() {
				$(this).removeClass("ui-corner-top").addClass("ui-corner-all");
			}
		});

		function entName(obj, state) {
			if ($('#name').val() == '') {
				var city = obj.value.toLowerCase();
				var uf = state.toLowerCase();
				$('#name').val(uf + '-' + city);
			}

			if ($('#latitude').val() == '') $('#latitude').val(obj.latitude);
			if ($('#longitude').val() == '') $('#longitude').val(obj.longitude);
		}
	});
</script>