<table id="filterMenu">
    <tr>
        <td style="text-align:left"><a href="<?=url::base()?>profiles/" class="filterMenu"><img
            src="<?=url::site('images/actions/arrow_left.png')?>" alt="Adicionar nova entidade"/>&nbsp;&nbsp;&nbsp;Voltar
            à listagem</a></td>
    </tr>
</table>

<form action="#" class="bForms">
    <fieldset>
        <legend>Informações básicas</legend>
        <span class="iblock">Nome do perfil: <strong><?=$profile->name?></strong> (<?=$profile->id?>)</span>
        <span class="iblock">Protocolo: <strong><?=$profile->verbose('protocol')?></strong></span><br/>
        <span class="iblock"><span class="label">Intervalo de Polling (segundos): </span><strong><?=$profile->polling?>
            ms</strong></span>
        <span class="iblock"><span class="label">Número de Vagões:: </span><strong><?=$profile->count?></strong></span>
        <span class="iblock"><span
            class="label">Número de Probes (por Vagão): </span><strong><?=$profile->probeCount?></strong></span>
        <span class="iblock"><span class="label">Tamanho do probe (bytes): </span><strong><?=$profile->probeSize?>
            bytes</strong></span>
        <span class="iblock"><span
            class="label">Intervalo entre vagões (milisegundos): </span><strong><?=$profile->gap?> ms</strong></span>
        <span class="iblock"><span class="label">Tempo de expiração (segundos): </span><strong><?=$profile->timeout?>
            s</strong></span>
        <span class="iblock"><span class="label"><?=$profile->title('qosType')?>
            : </span><strong><?=$profile->verbose('qosType')?></strong></span>
        <span class="iblock"><span class="label"><?=$profile->title('qosValue')?>
            : </span><strong><?=$profile->verbose('qosValue')?></strong></span><br/><br/>
        <span class="iblock"><span class="label"><?=$profile->title('description')?>
            : </span><i><?=$profile->description?></i></span>

    </fieldset>
    <fieldset>
        <legend>Métricas Cobertas</legend>
        <?php foreach ($profile->metrics as $metric): ?>
        <span class="iblock"><span class="label"><?=$metric->name?></span> <i>(<?=$metric->desc?>)</i></span><br/>
        <?php endforeach; ?>
    </fieldset>
</form>