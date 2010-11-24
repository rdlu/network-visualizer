<table id="filterMenu">
    <tr>
        <td>Cidade de Origem da Medição:
            <input type="text" name="cidade" id="cidade" size="50"/>
        </td>
    </tr>
</table>

<script type="text/javascript">
	$(function() {
		function log( message ) {
			console.info(message);
		}

		$( "#cidade" ).autocomplete({
			source: function( request, response ) {
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
					success: function( data ) {
						response( $.map( data.geonames, function( item ) {
							return {
								label: item.name + (item.adminName1 ? ", " + item.adminName1 : "") + ", " + item.countryName,
								value: item.name + (item.adminName1 ? ", " + item.adminName1 : "")
							}
						}));
					}
				});
			},
			minLength: 2,
			select: function( event, ui ) {
				log( ui.item ?
					"Selected: " + ui.item.label :
					"Nothing selected, input was " + this.value);
			},
			open: function() {
				$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
			},
			close: function() {
				$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
			}
		});
	});
</script>
<style type="text/css">
.ui-autocomplete-loading { background: white url('images/loading/16.gif') right center no-repeat; }
</style>

