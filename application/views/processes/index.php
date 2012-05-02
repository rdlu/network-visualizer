<table id="filterMenu">
	<tr>
		<td>Sonda de Origem da Medição:
			<input type="text" name="sonda" id="sonda" size="30"
                value="<?=(count($defaultManager))?$defaultManager['name'].' ('.$defaultManager['ipaddress'].')':''?>"/>
			<img id="sondaSelect" style="border: solid thin #666; background-color: #eee; vertical-align: middle;" src="<?=url::base()?>/images/actions/button_down.png" />
				
		</td>
		<td><a href="<?=url::base()?>processes/new/" class="filterMenu" id="newlink"><img
				src="<?=url::site('images/actions/clock_add.png')?>" alt="Adicionar nova processo de medição"/>&nbsp;&nbsp;&nbsp;Agendar
			novo processo de medição</a></td>
	</tr>
</table>
<div id="listaSondas"></div>

<script type="text/javascript">
	var newlink = '';
	var sondaAuto;
	$(function() {

		$("#sondaSelect").click(function(evt) {
			sondaAuto.autocomplete("search","topten");
		});

		newlink = $('#newlink').attr('href');

		sondaAuto = $("#sonda").autocomplete({
			source: function(request, response) {
				$.ajax({
					url: "<?=url::site('entities/topTenManagers')?>",
					type: 'post',
					data: {
						maxRows: 5,
						name: request.term
					},
					success: function(data) {
						console.log(data);
						response($.map(data.entities, function(item) {

							return {
								label: item.name + ' (' + item.ipaddress + ')',
								value: item.name + ' (' + item.ipaddress + ')',
								city: item.city,
								state: item.state,
								id: item.id,
								ipaddress: item.ipaddress
							}
						}));
					},
					error: function(status, msg, error) {
						console.error(msg);
					}
				});
			},
			minLength: 2,
			select: function(event, ui) {
				console.log(ui.item ?
						"Selected: " + ui.item.label :
						"Nothing selected, input was " + this.value);
				$("#sonda").data("id", ui.item);
				listProcesses(ui.item);
			},
			open: function() {
				$(this).removeClass("ui-corner-all").addClass("ui-corner-top");
			},
			close: function() {
				$(this).removeClass("ui-corner-top").addClass("ui-corner-all");
			}
		});
	});

    <?php if(count($defaultManager)): ?>
    jQuery("#sonda").data("id",<?=$defaultManager['id']?>);
    listProcesses({id:<?=$defaultManager['id']?>,ipaddress:"<?=$defaultManager['ipaddress']?>"});
    <?php endif; ?>

	function listProcesses(sonda) {
		if (sonda.id != 0) $('#newlink').attr('href', '<?=url::base()?>processes/new/' + sonda.id);
		else $('#newlink').attr('href', newlink);
		jQuery.ajax({
			url: "<?=url::site('processes/list')?>/" + sonda.ipaddress,
			type: 'get',
			beforeSend: function() {
				jQuery('#listaSondas').addClass('loadingBig');
			},
			complete: function() {
				jQuery('#listaSondas').removeClass('loadingBig');
			},
			success: function(data) {
				jQuery("#listaSondas").html(data);
			},
			error: function(status, msg, error) {
				console.log(error);
			}
		});
	}

	function removeProcess (sid, src, did, dest) {
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
                                    console.log(message+idx);
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
                                                if (undefined!=data.message[4]) {
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
</script>
<style type="text/css">
	.ui-autocomplete-loading {
		background: white url('<?=url::site()?>images/loading/16.gif') right center no-repeat;
	}
</style>

