<?php if ($errors): ?>
<ul class="errors">
    <?php foreach ($errors as $error): ?>
    <li class="error"><?=$error?></li>
    <?php endforeach; ?>
</ul>
<?php endif ?>

<table id="entityList" class="tablesorter">
    <thead>
    <tr>
        <th>Origem</th>
        <th>Destino</th>
        <th>Métricas cobertas</th>
        <th>Última atualização</th>
        <th>Ações</th>
    </tr>
    </thead>
    <tbody>
    <?php if (isset($destinations) > 0): ?>
        <?php foreach ($destinations as $destination): ?>
        <tr>
            <td><a
                href="<?=Url::site('entities/view') . '/' . $source->id?>"><?=$source->name . ' (' . $source->ipaddress?>
                )</a></td>
            <td><a
                href="<?=Url::site('entities/view') . '/' . $destination->id?>"><?=$destination->name . ' (' . $destination->ipaddress?>
                )</a></td>
            <td><?php foreach ($metrics[$destination->id] as $metric) echo $metric->name . ' '; ?></td>
            <td><?=date('Y-m-d H:i:s', $destination->updated)?></td>
            <td>
                <a href="#"><img src="<?=url::site('images/actions/clock_delete.png')?>" alt="Remover"
                                 onclick="removeProcess(<?=$source->id?>,'<?=$source->name . ' (' . $source->ipaddress . ')'?>',<?=$destination->id?>,'<?=$destination->name . ' (' . $destination->ipaddress . ')'?>')"/></a>
            </td>
        </tr>

            <? endforeach ?>

        <?php else: ?>
    <tr>
        <td colspan="5">Nenhum processo encontrado com endereço de origem <b><?=$sourceAddr?></b></td>
    </tr>
        <?php endif ?>
    </tbody>
</table>
<script type="text/javascript">
    $(function () {
        $('#entityList').tablesorter({
            'headers':{
                3:{sorter:false},
                4:{sorter:false}
            },
            'widgets':['zebra']
        });
    });
</script>