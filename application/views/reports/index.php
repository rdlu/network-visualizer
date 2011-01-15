<table id="filterMenu">
	<tr>
        <td>Sonda de Origem do Teste (Gerente):&nbsp;
            <input type="text" name="sonda" id="sonda" size="36"/>
        </td>
		<td>Sonda de Destino do Teste (Agente):&nbsp;
	        <select name="destino" id="destino" disabled="true">
		        <option value="0"> :: Selecione a Sonda de Origem primeiro :: </option>
	        </select></td>
	</tr>
	<tr>
		<td><b>Início:</b>&nbsp;&nbsp;&nbsp;&nbsp;Data
			<input id="inicio" name="inicio" type="text" size="10" value="<?=date("d/m/Y", mktime(0, 0, 0, date("m"),date("d")-1,date("Y")))?>" />
			&nbsp;&nbsp;&nbsp;&nbsp;Hora&nbsp;
			<input id="horaini" type="text" size="6" value="<?=date('H:i')?>" />

		</td>
		<td><b>Fim:</b>&nbsp;&nbsp;&nbsp;&nbsp;Data&nbsp;
			<input id="fim" name="fim" type="text" size="10" value="<?=date('d/m/Y')?>" />
			&nbsp;&nbsp;&nbsp;&nbsp;Hora:&nbsp;
			<input id="horafim" type="text" size="6" value="<?=date('H:i')?>" /></td>
	</tr>
	<tr>
		<td colspan="2"><span class="button" id="consultar"><img src="<?=url::base()?>images/actions/tick.png" alt="Consultar">&nbsp;Consultar</span></td>
	</tr>
</table>
<div id="resultado"></div>

<script type="text/javascript">
	$(function() {

		$("#consultar").click(function(evt) {
			//log($("#sonda").data("id") + $("#destino").val());
			//checa se tudo foi preenchido corretamente
			if($("#sonda").val().length>0 && $("#destino").val() != 0) {
				//Entao faz a requisição ajax
				jQuery.ajax({
					url: "<?=url::site('reports/view')?>",
                    type: 'post',
					data: {
						source: $("#sonda").data("id"),
						destination: $("#destino").val(),
						startDate: $("#inicio").val(),
						startHour: $("#horaini").val(),
						endDate: $("#fim").val(),
						endHour: $("#horafim").val()
					},
					success: function( data ) {
                  $("#resultado").html(data);
					},
					error: function(status,msg,error) {
						err(msg);
					}
				});
			} else {
				var $dialog = $('<div></div>').html('A sonda de origem e/ou destino não foram escolhidas.').dialog({
					autoOpen: true,
					modal: true,
					title: 'Campos obrigatórios',
					buttons: {
						Ok: function() {
							$( this ).dialog( "close" );
						}
					}
				});
			}
		});

		$("#inicio").inputmask("d/M/y");
		$("#fim").inputmask("d/M/y");
		$("#horafim").inputmask("h:m");


		function log( message ) {
			console.info(message);
		}

		function err(msg) {
			console.error(msg);
		}

		$("#sonda").autocomplete({
			source: function( request, response ) {
				$.ajax({
					url: "<?=url::site('entities/list')?>",
                    type: 'post',
					data: {
						maxRows: 5,
						name: request.term
					},
					success: function( data ) {
                  console.log(data);
						response( $.map( data.entities, function( item ) {

							return {
								label: item.name + ' ('+item.ipaddress+')',
								value: item.name + ' ('+item.ipaddress+')',
								city: item.city,
								state: item.state,
								id: item.id
							}
						}));
					},
					error: function(status,msg,error) {
						err(msg);
					}
				});
			},
			minLength: 2,
			select: function( event, ui ) {
				log( ui.item ?
					"Selected: " + ui.item.label :
					"Nothing selected, input was " + this.value);
				$("#sonda").data("id",ui.item.id);
				getDestinations(ui.item.id);
			},
			open: function() {
				$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
			},
			close: function() {
				$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
			}
		});


		function getDestinations(id) {
			$.ajax({
				url: "<?=url::site('entities/destinations')?>",
				type: 'post',
				data: {
					id: id,
				},
				success: function( data ) {
					$("#destino").html("");
					if(data.length > 0) {
						jQuery.each(data,function(idx,el) {
							$("<option value='"+el.id+"'>"+el.name+" ("+el.ipaddress+")</option>").appendTo("#destino");
						});
						$('#destino')[0].disabled = false;
						$('#destino')[0].focus();
					} else {
						$("<option value='0'> :: Esta sonda não realiza medições :: </option>").appendTo("#destino");
					}

				},
				error: function(status,msg,error) {
					console.error(msg);
					$("#destino").html("");
               $('#destino')[0].disabled = true;
               $("<option value='0'> :: Selecione a Sonda de Origem primeiro :: </option>").appendTo("#destino");
				}
			});
		}

		/* Brazilian initialisation for the jQuery UI date picker plugin. */
		/* Written by Leonildo Costa Silva (leocsilva@gmail.com). */
		$.datepicker.regional['pt-BR'] = {
			closeText: 'Fechar',
			prevText: '&#x3c;Anterior',
			nextText: 'Pr&oacute;ximo&#x3e;',
			currentText: 'Hoje',
			monthNames: ['Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho',
			'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
			monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun',
			'Jul','Ago','Set','Out','Nov','Dez'],
			dayNames: ['Domingo','Segunda-feira','Ter&ccedil;a-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sabado'],
			dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
			dayNamesMin: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
			weekHeader: 'Sm',
			dateFormat: 'dd/mm/yy',
			firstDay: 0,
			isRTL: false,
			showMonthAfterYear: false,
			yearSuffix: ''};
		$.datepicker.setDefaults($.datepicker.regional['pt-BR']);

		$.datepicker.setDefaults( $.datepicker.regional[ "pt-BR" ] );
		$( "#inicio" ).datepicker({
			changeMonth: true,
			changeYear: true,
		});

		$( "#fim" ).datepicker({
			changeMonth: true,
			changeYear: true,
		});

	});
</script>
<style type="text/css">
.ui-autocomplete-loading { background: white url('<?=url::base()?>/images/loading/16.gif') right center no-repeat; }
</style>