<table id="filterMenu">
    <tr>
        <td>
            <a href="<?=url::base().Request::instance()->controller?>" class="filterMenu">Voltar</a>
        </td>
        <td>
            <i>Cadastro de Novo Processo de Medição</i>
        </td>
    </tr>
</table>
<?php if($errors): ?>
<ul>
    <?php foreach($errors as $error): ?>
    <li><?=$error?></li>
    <?php endforeach; ?>
</ul>
<?php endif ?>

<?=Form::open(Request::instance()->controller.'/setup/'.$sourceEntity->id,array('id'=>'newEntity','class'=>'bForms'))?>
<fieldset title="Dados da Sonda de Origem">
    <legend>Dados da Sonda de Origem</legend>
    <img src="<?=url::site('images/boardMenu/source.png')?>" alt="Sonda de Origem" style="float:left"/>
    <table class="bForms">
        <tr>
            <td class="input">Nome: </td>
            <td><?=$sourceEntity->name?></td>
            <td class="input">Endereço IPv4: </td>
            <td><?=$sourceEntity->ipaddress?></td>
        </tr>
        <tr>
            <td class="input">Cidade: </td>
            <td><?=$sourceEntity->city?></td>
            <td class="input">Estado: </td>
            <td><?=$sourceEntity->state?></td>
        </tr>
    </table>
</fieldset>
<fieldset title="Dados da Sonda de Destino">
    <legend>Dados da Sonda de Destino</legend>
    <img src="<?=url::site('images/boardMenu/destination.png')?>" alt="Sonda de Destino" style="float:left"/>
    <table class="bForms">
        <tr>
            <td><label for="cidade">Cidade de Destino:</label>
                <input type="text" name="cidade" id="cidade" size="30"/>
            </td>
            <td><label for="sonda" style="width:200px">Sonda de Destino da Medição:</label><select name="sonda" id="sonda" disabled>
            <option value="0">-- Informe a cidade primeiro -- </option></select></td>
        </tr>
    </table>
</fieldset>
<fieldset title="Dados do Perfil a ser utilizado">
    <legend>Dados do Perfil a ser utilizado</legend>
    <img src="<?=url::site('images/boardMenu/profiles.png')?>" alt="Sonda de Destino" style="float:left"/>
    <table class="bForms">
        <tr>
            <td width="33%"><label for="profile">Perfil de Medição: </label>
                <select name="profile" id="profile">
                    <option value="0">-- Selecione um perfil --</option>
                    <?php foreach($profiles as $profile) {
                        echo "<option value='$profile->id'>$profile->name</option>";
                    }?>
                </select>
            </td>
            <td width="33%">Métricas cobertas: <span id="metricas">&nbsp;</span></td>
            <td width="33%">Descrição: <span id="description">&nbsp;</span></td>
        </tr>
    </table>
</fieldset>
<?=Form::submit('submit_'.Request::instance()->controller,'OK')?>
<?=Form::close()?>

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

    jQuery('#profile').change(function(obj) {
        var pid = obj.target.value;
        if(obj.target.value!=0)
            jQuery.ajax({
                url: "<?=url::site('profiles/info')?>",
                type: 'post',
                data: {
                    profile: pid
                },
                success: function( data ) {
                    console.log(data);
                    $("#metricas").html("");
                    jQuery.each(data.metrics,function(idx,el) {
                        console.log(el);
                        $('#metricas').append("&nbsp;"+el);
                    });
                    $("#description").html(data.description);
                },
                error: function(status,msg,error) {
                    console.log(error);
                    jQuery('#metricas').html('&nbsp;');
                    jQuery('#description').html('&nbsp;');
                }
            });
        else {
            jQuery('#metricas').html('&nbsp;');
            jQuery('#description').html('&nbsp;');
        }
    });
});
</script>