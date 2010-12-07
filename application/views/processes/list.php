<?php if($errors): ?>
<ul class="errors">
    <?php foreach($errors as $error): ?>
    <li class="error"><?=$error?></li>
    <?php endforeach; ?>
</ul>
<?php endif ?>

<table id="entityList" class="tablesorter">
    <thead>
    <tr>
        <th>Origem</th>
        <th>Destino</th>
        <th>Perfil</th>
        <th>Métricas cobertas</th>
        <th>Ações</th>
    </tr>
    </thead>
<tbody>
<?php if(count($processes) > 0): ?>
    <?php foreach($processes as $process): ?>
            <?php $destination = $process->destination->load();
                  $source = $process->source->load(); ?>
        <tr>
            <td><a href="<?=Url::site('entities/view').'/'.$source->id?>"><?=$source->name.' ('.$source->ipaddress?>)</a></td>
            <td><a href="<?=Url::site('entities/view').'/'.$destination->id?>"><?=$destination->name.' ('.$destination->ipaddress?>)</a></td>
            <td><?=$process->profile->load()->name?></td>
            <td><?php foreach($process->profile->load()->metrics as $metric) echo $metric->name.' '; ?></td>
            <td>
                <a href="<?=url::site('processes/remove').'/'.$process->id?>"><img src="<?=url::site('images/actions/clock_delete.png')?>" alt="Remover" /></a>
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
$(function(){
	$('#entityList').tablesorter({
		'headers': {
            3: {sorter: false},
			4: {sorter: false}
		},
		'widgets': ['zebra']
	});
});
</script>