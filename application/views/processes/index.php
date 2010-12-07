<table id="filterMenu">
    <tr>
        <td>Cidade de Origem:
            <input type="text" name="cidade" id="cidade" size="30"/>
        </td>
        <td>Sonda de Origem da Medição: <select name="sonda" id="sonda" disabled>
            <option value="0">-- Informe a cidade primeiro -- </option></select></td>
        <td><a href="<?=url::base()?>processes/new/" class="filterMenu" id="newlink"><img src="<?=url::site('images/actions/clock_add.png')?>" alt="Adicionar nova processo de medição" />&nbsp;&nbsp;&nbsp;Agendar novo processo de medição</a></td>
    </tr>
</table>
<div id="listaSondas"></div>

<script type="text/javascript">
	$(function() {
		function log( message ) {
			console.info(message);
		}

		$( "#cidade" ).autocomplete({
			source: function( request, response ) {
				$.ajax({
					url: "<?=url::site('tools/cities')?>",
                    type: 'post',
					data: {
						maxRows: 5,
						startsWith: request.term,
                        country: 'br'
					},
					success: function( data ) {
                        console.log(data);
						response( $.map( data.geonames, function( item ) {

							return {
								label: item.city + (item.state ? ", " + item.state : ""),
								value: item.city + (item.state ? ", " + item.state : ""),
                                city: item.city,
                                state: item.state
							}
						}));
					},
                    error: function(status,msg,error) {
                        console.error(msg);
                        $("#sonda").html("");
                        $("<option value='0'>-- Informe a cidade primeiro -- </option>").appendTo("#sonda");
                    }
				});
			},
			minLength: 2,
			select: function( event, ui ) {
				log( ui.item ?
					"Selected: " + ui.item.label :
					"Nothing selected, input was " + this.value);
                sondaCity(ui.item.city, ui.item.state);
			},
			open: function() {
				$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
			},
			close: function() {
				$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
			}
		});

        function sondaCity(city,state) {
            $.ajax({
					url: "<?=url::site('entities/byCity')?>",
                    type: 'post',
                    data: {
                        city: city,
                        state: state
                    },
					success: function( data ) {
                        console.log(data);
                        $("#sonda").html("");
                        $("<option value='0'>-- Selecione a sonda -- </option>").appendTo("#sonda");
                        jQuery.each(data.entities,function(idx,el) {
                            $("<option value='"+el.ipaddress+"'>"+el.name+" ("+el.ipaddress+")</option>").appendTo("#sonda");
                        });
                        $('#sonda')[0].disabled = false;
                        $('#sonda')[0].focus();
					},
                    error: function(status,msg,error) {
                        console.log(error);
                        $("#sonda").html("");
                        $('#sonda')[0].disabled = true;
                        $("<option value='0'>-- Informe a cidade primeiro -- </option>").appendTo("#sonda");
                    }
				});
        }

        var newlink = $('#newlink').attr('href');

        $('#sonda').change(function(obj){
            console.log('Selecionada a sonda '+obj.target.value);
            if(obj.target.value != 0) $('#newlink').attr('href',newlink+obj.target.value);
            else $('#newlink').attr('href',newlink);
            $.ajax({
					url: "<?=url::site('processes/list')?>/"+obj.target.value,
                    type: 'get',
					success: function( data ) {
                        jQuery("#listaSondas").html('');
                        jQuery("#listaSondas").append(data);
					},
                    error: function(status,msg,error) {
                        console.log(error);
                    }
				});
        });
	});
</script>
<style type="text/css">
.ui-autocomplete-loading { background: white url('images/loading/16.gif') right center no-repeat; }
</style>

