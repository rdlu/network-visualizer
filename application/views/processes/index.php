<table id="filterMenu">
    <tr>
        <td>Sonda de Origem da Medição:
	        <input type="text" name="sonda" id="sonda" size="30"/>
        </td>
        <td><a href="<?=url::base()?>processes/new/" class="filterMenu" id="newlink"><img src="<?=url::site('images/actions/clock_add.png')?>" alt="Adicionar nova processo de medição" />&nbsp;&nbsp;&nbsp;Agendar novo processo de medição</a></td>
    </tr>
</table>
<div id="listaSondas"></div>

<script type="text/javascript">
	$(function() {

		var newlink = $('#newlink').attr('href');

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
								id: item.id,
								ipaddress: item.ipaddress
							}
						}));
					},
					error: function(status,msg,error) {
						console.error(msg);
					}
				});
			},
			minLength: 2,
			select: function( event, ui ) {
				console.log( ui.item ?
					"Selected: " + ui.item.label :
					"Nothing selected, input was " + this.value);
				$("#sonda").data("id",ui.item.id);
				listProcesses(ui.item);
			},
			open: function() {
				$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
			},
			close: function() {
				$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
			}
		});

       function listProcesses(sonda){
            if(sonda.id != 0) $('#newlink').attr('href',newlink+sonda.ipaddress);
            else $('#newlink').attr('href',newlink);
            $.ajax({
					url: "<?=url::site('processes/list')?>/"+sonda.ipaddress,
                    type: 'get',
					success: function( data ) {
                        jQuery("#listaSondas").html('');
                        jQuery("#listaSondas").append(data);
					},
                    error: function(status,msg,error) {
                        console.log(error);
                    }
				});
       }
	});
</script>
<style type="text/css">
.ui-autocomplete-loading { background: white url('images/loading/16.gif') right center no-repeat; }
</style>

