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
        <?php if ($entity->isAndroid): ?>
        <br/><br/><span
                class="iblock status info">Este agente android está programado para medições a cada <b><?=$entity->polling / 60?>
            minutos</b></span>
        <?php endif; ?>
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
                <?php if (Auth::instance()->logged_in('admin')): ?>
                <img src="<?=url::base()?>images/actions/clock_delete.png" alt="Remover"
                     onclick="deleter.removeProcess(<?=$entity->id?>,'<?=$entity->name?>',<?=$destination['id']?>,'<?=$destination['name']?>')">
                <?php endif; ?>
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
            <li>
                <a href="<?=Url::site('entities/view') . '/' . $source['id']?>"><?=$source['name']?>
                    (<?=$source['ipaddress']?>)</a>
                <?php if (Auth::instance()->logged_in('admin')): ?>
                <img src="<?=url::base()?>images/actions/clock_delete.png" alt="Remover"
                     onclick="deleter.removeProcess(<?=$source['id']?>,'<?=$source['name']?>',<?=$entity->id?>,'<?=$entity->name?>')">
                <?php endif; ?>
            </li>
            <?php endforeach; endif;  ?>
        </ul>
    </fieldset>
    <fieldset>
        <legend>Avançado:</legend>
        <?php foreach ($version = $status->getVersion(true) as $k => $v): ?>
        <span class="iblock"><span class="label"><?= $k ?>
            : </span><strong><?=$v?></strong></span><?php //if($k == 'version')echo '&nbsp;&nbsp;<a href="'.'">HEY</a>'; ?>
        <br/>
        <?php endforeach; ?><br/><br/>
        <?php if (Auth::instance()->logged_in('config')): ?>
        <span class="button" id="remover" onclick="deleter.removeSonda();">
			<img src="<?=url::base()?>images/actions/cross.png" alt="Remover">
			Remover
		</span>&nbsp;
        <span class="button" id="checkRRD">
			<a href="<?=url::site('entities/checkRRD') . '/' . $entity->id?>"><img
                    src="<?=url::base()?>images/actions/folder_wrench.png" alt="Checar RRD">
                Checar RRD</a>
		</span>&nbsp;
        <?php if ($entity->isAndroid): ?>
            <span class="button" id="changePolling" onclick="polling.dialogOpen();">
                <img src="<?=url::base()?>images/actions/clock_edit.png" alt="Checar RRD">
                Trocar intervalo de medição
            </span>
            <?php endif; ?>
        <?php endif; ?>
    </fieldset>
</form>

<script type="text/javascript">
var processes = <?=$procJSON?>;
var sources = <?=Zend_Json::encode($sources)?>;
var sourcesProcesses = <?=Zend_Json::encode($sourcesProcesses)?>;
var destinations = <?=Zend_Json::encode($destinations)?>;
var destinationsProcesses = <?=Zend_Json::encode($destinationsProcesses)?>;
var myself = <?=Zend_Json::encode($entity->as_array())?>;

<?php if (Auth::instance()->logged_in('admin')): ?>
var deleter = {
    html:function () {
        var destinationHTML = '';
        jQuery.each(destinations, function (idx, el) {
            destinationHTML += "<li id=\"pair-" + myself.id + '-' + el.id + "\">" + myself.name + ' -&gt; ' + el.name + '</li>';
        });

        var sourceHTML = '';
        jQuery.each(sources, function (idx, el) {
            sourceHTML += "<li class=\"pair remove\" id=\"pair-" + el.id + '-' + myself.id + "\" >" + el.name + ' -&gt; ' + myself.name + '</li>';
        });
        return '<ul id="listaDeleter" class="lista deleter">' + destinationHTML + sourceHTML + '</ul>';
    },
    removeProcess:function (sid, src, did, dest) {
        var dialog = $('<div></div>').html('Deseja remover os processos de medição, entre ' + src + ' e ' + dest + '?').dialog({
            autoOpen:true,
            modal:true,
            minWidth:500,
            title:'Remover processo de medição (S:' + sid + ' D:' + did + ')',
            buttons:{
                Cancelar:function () {
                    $(this).dialog("close");
                },
                OK:function () {
                    jQuery.ajax({
                        url:"<?=url::site('processes/remove')?>",
                        type:'post',
                        data:{'source':sid, 'destination':did},
                        beforeSend:function () {
                            dialog.html("Removendo processo, aguarde...");
                            dialog.dialog("option", "buttons", {});
                        },
                        success:function (data) {
                            if (data.errors > 0) {
                                var msg = '';
                                jQuery.each(data.message, function (idx, message) {
                                    console.log(message + idx);
                                    if (idx != 0) msg += message + '<br />';
                                });
                                dialog.html("<b>Não foi possível remover o processo:</b><br />" + msg + "Você pode forçar a remoção, em caso das sondas já terem sido desativadas.");
                                dialog.dialog("option", "buttons", {
                                    Cancelar:function () {
                                        dialog.dialog("close");
                                    },
                                    "Forçar remoção":function () {
                                        jQuery.ajax({
                                            url:"<?=url::site('processes/remove')?>",
                                            type:'post',
                                            data:{'source':sid, 'destination':did, 'force':true},
                                            beforeSend:function () {
                                                dialog.html("Removendo processo em modo forçado, aguarde...");
                                                dialog.dialog("option", "buttons", {});
                                            },
                                            success:function (data) {
                                                if (undefined != data.message[4]) {
                                                    var msg = '';
                                                    jQuery.each(data.message, function (idx, message) {
                                                        if (idx != 0) msg += message + '<br />';
                                                    });
                                                    dialog.html("<b>Não foi possível remover o processo:</b><br />" + msg);
                                                    dialog.dialog("option", "buttons", {
                                                        Cancelar:function () {
                                                            dialog.dialog("close");
                                                        }
                                                    });
                                                } else {
                                                    dialog.html("O processo foi removido com sucesso!");
                                                    dialog.dialog("option", "buttons", {
                                                        OK:function () {
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
                                    OK:function () {
                                        window.location.reload();
                                    }
                                });
                            }
                        },
                        error:function (status, msg, error) {
                            console.log(error);
                        }
                    });
                }
            }
        });
    },
    removeSonda:function () {
        if ((typeof(processes.length) != 'undefined') && (processes.length > 0)) {
            var dialog3 = $('<div></div>').html('A sonda ainda tem processos rodando, você deve desagendá-los antes.').dialog({
                autoOpen:true,
                modal:true,
                minWidth:500,
                title:'Remover sonda <?=$entity->name?>',
                buttons:{
                    OK:function () {
                        $(this).dialog("close");
                    }
                }
            });
        } else {
            var dialog2 = $('<div></div>').html('Você deseja remover a sonda <?=$entity->name?>?').dialog({
                autoOpen:true,
                modal:true,
                minWidth:500,
                title:'Remover sonda <?=$entity->name?>',
                buttons:{
                    OK:function () {
                        jQuery.ajax({
                            url:"<?=url::site('entities/remove')?>",
                            type:'post',
                            data:{'id':<?=$entity->id?> },
                            beforeSend:function () {
                                dialog2.html("Removendo sonda, aguarde...");
                                dialog2.dialog("option", "buttons", {});
                            },
                            success:function (data) {
                                if (!data.error) {
                                    dialog2.html(data);
                                    dialog2.dialog("option", "buttons", {
                                        OK:function () {
                                            dialog2.dialog("close");
                                            window.location = "<?=url::base()?>entities/";
                                        }
                                    });
                                } else {
                                    dialog2.html(data);
                                    dialog2.dialog("option", "buttons", {
                                        OK:function () {
                                            dialog2.dialog("close");
                                        }
                                    });
                                }
                            }
                        });
                    },
                    Cancelar:function () {
                        $(this).dialog("close");
                    }
                }
            });
        }
    }
};
    <?php endif; ?>

<?php if ($entity->isAndroid): ?>
var polling = {
    field:$("#polling"),
    dialog:$('<div id="dialog-polling" title="Troca de intervalo de medição"><p class="validateTips"></p><br /><form><fieldset><label for="polling">Novo intervalo em MINUTOS: </label><input type="text" name="polling" id="polling" class="text ui-widget-content ui-corner-all"/></fieldset></form></div>').dialog({
        autoOpen:false,
        height:170,
        width:400,
        modal:true,
        buttons:{
            OK:function () {
                var bValid = true;
                polling.field.removeClass("ui-state-error");

                bValid = bValid && polling.checkLength(polling.field, "polling", 1, 16);
                bValid = bValid && polling.checkRegexp(polling.field, /^([0-9])+$/, "Somente números no campo de intervalo");

                if (bValid) {
                    var changed = polling.change(polling.field.val() * 60);
                    //$( this ).dialog( "close" );
                }
            },
            Cancelar:function () {
                $(this).dialog("close");
            }
        },
        close:function () {
            polling.field.val("").removeClass("ui-state-error");
        }
    }),
    dialogOpen:function () {
        polling.dialog.dialog("open");
        polling.field = $("#polling");
    },
    change:function (seconds) {
        var dialog = $("#dialog-polling");
        //funcoes ajax
        jQuery.ajax({
            url:"<?=url::site('entities/setPolling')?>/<?=$entity->id?>",
            type:'post',
            data:{'polling':seconds},
            beforeSend:function () {
                dialog.html("Enviando novos parâmetros, aguarde...");
                dialog.dialog("option", "buttons", {});
            },
            success:function (data) {
                if (data.errors > 0) {
                    var msg = '';
                    jQuery.each(data.message, function (idx, message) {
                        console.log(message + idx);
                        if (idx != 0) msg += message + '<br />';
                    });
                    dialog.html("<b>Não foi possível alterar o intervalo de medição:</b><br />" + msg + "Verifique se o gerente desta sonda está online.");
                    dialog.dialog("option", "buttons", {
                        Cancelar:function () {
                            dialog.dialog("close");
                        }
                    });
                } else {
                    dialog.html("O intervalo foi alterado com sucesso!");
                    dialog.dialog("option", "buttons", {
                        OK:function () {
                            window.location.reload();
                        }
                    });
                }
            },
            error:function (status, msg, error) {
                console.log(error);
            }
        });
    },
    updateTips:function (t) {
        $(".validateTips")
                .text(t)
                .addClass("ui-state-highlight");
        setTimeout(function () {
            $(".validateTips").removeClass("ui-state-highlight", 1500);
        }, 500);
    },
    checkLength:function (o, n, min, max) {
        if (o.val().length > max || o.val().length < min) {
            o.addClass("ui-state-error");
            polling.updateTips("O campo " + n + " deve possuir um valor.");
            return false;
        } else {
            return true;
        }
    },
    checkRegexp:function (o, regexp, n) {
        if (!( regexp.test(o.val()) )) {
            o.addClass("ui-state-error");
            polling.updateTips(n);
            return false;
        } else {
            return true;
        }
    }
};
    <?php endif; ?>
</script>