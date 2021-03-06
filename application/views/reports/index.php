<table id="reportsMenu">
    <tr>
        <td>Sonda de Origem (Gerente):&nbsp;
            <input type="text" name="sonda" id="sonda" size="36" tabindex="1"
                   value="<?=(count($defaultManager)) ? $defaultManager['name'] . ' (' . $defaultManager['ipaddress'] . ')' : ''?>"/>
            <img id="sondaSelect" style="border: solid thin #666; background-color: #eee; vertical-align: middle;"
                 src="<?=url::base()?>/images/actions/button_down.png"/>
        </td>
        <td><b>Início:</b>&nbsp;&nbsp;&nbsp;&nbsp;Data
            <input id="inicio" name="inicio" type="text" tabindex="4" size="10"
                   value="<?=date("d/m/Y", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")))?>"/>
            &nbsp;&nbsp;&nbsp;&nbsp;Hora&nbsp;
            <input id="horaini" type="text" tabindex="5" size="6" value="<?=date('H:i')?>"/>
        </td>

    </tr>
    <tr>
        <td>Tipo de destino:
        	<span id="destAndroid">
        		<input type="radio" name="destType" id="radioDestAndroid" tabindex="2" value="1" checked="checked"/>
        		<label for="radioDestAndroid"><img src="<?=url::base()?>images/android16.png" alt=""
                                                   style="vertical-align: -3px"> Android</label>
        	</span>
			<span id="destLinux">
				<input type="radio" name="destType" tabindex="2" id="radioDestLinux" value="0"/>
				<label for="radioDestLinux"><img src="<?=url::base()?>images/ubuntu16.png" alt=""
                                                 style="vertical-align: -3px"> Linux</label>
			</span>
        </td>

        <td><b>Fim:</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Data&nbsp;
            <input id="fim" name="fim" type="text" size="10" value="<?=date('d/m/Y')?>" tabindex="6"/>
            &nbsp;&nbsp;&nbsp;&nbsp;Hora&nbsp;
            <input id="horafim" type="text" size="6" value="<?=date('H:i')?>" tabindex="7"/>
            <span id="fimNow" style="cursor: pointer"><u>Agora</u></span>
        </td>
    </tr>
    <tr>
        <td>Sonda de Destino (Agente):&nbsp;
            <select name="destino" id="destino" disabled="true" tabindex="3">
                <option value="0"> :: Selecione a Sonda de Origem primeiro ::</option>
            </select>
        </td>

        <td>
            <span class="input nice button" id="consultar" style="box-shadow:2px 2px 2px #555; margin: 5px"><img
                src="<?=url::base()?>images/actions/tick.png" alt="Consultar">&nbsp;Consultar</span>

            <div id="formatoEscolha" style="display: inline-block;">
                <small style="font-size: 10px">Formato:</small>
                <span id="typeSQL"><input type="radio" name="dbchoice" id="radioSQL" tabindex="8" value="sql"
                                          checked="checked"/><label for="radioSQL"
                                                                    style="font-size: 10px">Completo</label></span>
                <span id="typeImg"><input type="radio" name="dbchoice" id="radioImg" value="img" tabindex="9"/><label
                    for="radioImg" style="font-size: 10px">Resumido</label></span>
            </div>
        </td>
    </tr>
</table>
<div id="resultado"></div>

<div id="clipboardArea" style="display: none; width: 350px">
    <textarea name="tempArea" id="tempArea" cols="55" rows="10"></textarea>
    <table class="filterMenu">
        <tr>
            <td>
                <strong style="text-shadow: none">Instruções</strong><br/>
                <em style="font-size: 13px; text-shadow: none">
                    2. Aperte CTRL + C no seu teclado<br/>
                    3. Cole no Excel (CTRL + V)<br/>
                </em>
            </td>
        </tr>
    </table>
</div>
<div id="dialog-modal" title="Carregando, aguarde." style="display: none">Aguarde enquanto a informação requisitada é
    obtida...
</div>

<script type="text/javascript">
var sondaAuto;
function makeRequest(type) {
    var urlType;
    switch (type) {
        case 'flot':
            urlType = "<?=url::site('reports/viewFlot')?>";
            break;
        case 'img':
            urlType = "<?=url::site('reports/view')?>";
            break;
        case 'sql':
            urlType = "<?=url::site('reports/viewSql')?>";
            break;
        case 'xport':
            urlType = "<?=url::site('reports/viewXport')?>";
            break;
    }
    jQuery.ajax({
        url:urlType,
        type:'post',
        data:{
            source:$("#sonda").data("id"),
            destination:$("#destino").val(),
            startDate:$("#inicio").val(),
            startHour:$("#horaini").val(),
            endDate:$("#fim").val(),
            endHour:$("#horafim").val(),
            destType:$("input[name=destType]:checked").val()
        },
        beforeSend:function () {
            jQuery("#dialog-modal").dialog({modal:true});
        },
        success:function (data) {
            jQuery("#dialog-modal").dialog('close');
            $("#resultado").html("");
            $("#resultado").html(data);
        },
        error:function (status, msg, error) {
            jQuery("#dialog-modal").dialog('close');
            $("#resultado").html("Erro na obtenção da informação. Recarregue seu navegador (tecla F5) e tente novamente.");
            err(msg);
        }
    });
}

function consultaClick(evt) {
    //log($("#sonda").data("id") + $("#destino").val());
    //checa se tudo foi preenchido corretamente
    if ($("#sonda").val().length > 0 && $("#destino").val() != 0) {
        //Entao faz a requisição ajax
        makeRequest($("input:radio[name=dbchoice]:checked").val());
    } else {
        var $dialog = $('<div></div>').html('A sonda de origem e/ou destino não foram escolhidas.').dialog({
            autoOpen:true,
            modal:true,
            title:'Campos obrigatórios',
            buttons:{
                Ok:function () {
                    $(this).dialog("close");
                }
            }
        });
    }
}

$(function () {
    //default para isAndroid
    $('#formatoEscolha').hide();

    $("#fimNow").click(function () {
        $("#fim").val($.datepicker.formatDate('dd/mm/yy', new Date()));
        $("#horafim").val(new Date().getHours() + ':' + new Date().getMinutes());
    });

    $("#consultar").click(consultaClick);
    $("#reportsMenu").keydown(function (evt) {
        if (evt.which == 13) {
            consultaClick(evt);
        }
    });

    $("#inicio").inputmask("d/M/y");
    $("#horaini").inputmask("h:m");
    $("#fim").inputmask("d/M/y");
    $("#horafim").inputmask("h:m");

    $("input[name=destType]").click(function (evt) {
        if ($("input[name=destType]:checked").val() == 1) {
            $("#radioSQL").attr('checked', 'checked');
            $('#formatoEscolha').hide();
        } else {
            $('#formatoEscolha').show();
        }
        var id = $("#sonda").data("id");
        getDestinations(id);
    });


    function log(message) {
        console.info(message);
    }

    function err(msg) {
        console.error(msg);
    }

    $("#sondaSelect").click(function (evt) {
        sondaAuto.autocomplete("search", "topten");
    });

    sondaAuto = $("#sonda").autocomplete({
        source:function (request, response) {
            $.ajax({
                url:"<?=url::site('entities/topTenManagers')?>",
                type:'post',
                data:{
                    maxRows:5,
                    name:request.term
                },
                success:function (data) {
                    console.log(data);
                    response($.map(data.entities, function (item) {

                        return {
                            label:item.name + ' (' + item.ipaddress + ')',
                            value:item.name + ' (' + item.ipaddress + ')',
                            city:item.city,
                            state:item.state,
                            id:item.id
                        }
                    }));
                },
                error:function (status, msg, error) {
                    err(msg);
                }
            });
        },
        minLength:2,
        autoFocus:true,
        select:function (event, ui) {
            log(ui.item ?
                "Selected: " + ui.item.label :
                "Nothing selected, input was " + this.value);
            $("#sonda").data("id", ui.item.id);
            getDestinations(ui.item.id);
        },
        open:function () {
            $(this).removeClass("ui-corner-all").addClass("ui-corner-top");
        },
        close:function () {
            $(this).removeClass("ui-corner-top").addClass("ui-corner-all");
        }
    });

<?php if (count($defaultManager)): ?>
    jQuery("#sonda").data("id",<?=$defaultManager['id']?>);
    getDestinations(<?=$defaultManager['id']?>);
    <?php endif; ?>


    function getDestinations(id) {
        $.ajax({
            url:"<?=url::site('entities/destinations')?>",
            type:'post',
            data:{
                id:id,
                isAndroid:$("input[name=destType]:checked").val()
            },

            success:function (data) {
                $("#destino").html("");
                if (data.length > 0) {
                    jQuery.each(data, function (idx, el) {
                        $("<option value='" + el.id + "'>" + el.name + " (" + el.ipaddress + ")</option>").appendTo("#destino");
                    });
                    $('#destino')[0].disabled = false;
                    $('#destino')[0].focus();
                } else {
                    $("<option value='0'> :: Esta sonda não realiza medições deste tipo :: </option>").appendTo("#destino");
                }

            },
            error:function (status, msg, error) {
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
        closeText:'Fechar',
        prevText:'&#x3c;Anterior',
        nextText:'Pr&oacute;ximo&#x3e;',
        currentText:'Hoje',
        monthNames:['Janeiro', 'Fevereiro', 'Mar&ccedil;o', 'Abril', 'Maio', 'Junho',
            'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
        monthNamesShort:['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun',
            'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
        dayNames:['Domingo', 'Segunda-feira', 'Ter&ccedil;a-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sabado'],
        dayNamesShort:['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
        dayNamesMin:['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
        weekHeader:'Sm',
        dateFormat:'dd/mm/yy',
        firstDay:0,
        isRTL:false,
        showMonthAfterYear:false,
        yearSuffix:''};
    $.datepicker.setDefaults($.datepicker.regional['pt-BR']);

    $.datepicker.setDefaults($.datepicker.regional[ "pt-BR" ]);
    $("#inicio").datepicker({
        changeMonth:true,
        changeYear:true,
        showButtonPanel:true
    });

    $("#fim").datepicker({
        changeMonth:true,
        changeYear:true,
        showButtonPanel:true
    });

});
</script>
<style type="text/css">
    .ui-autocomplete-loading {
        background: white url('<?=url::base()?>/images/loading/16.gif') right center no-repeat;
    }
</style>