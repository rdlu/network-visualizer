<table id="filterMenu">
    <tr>
        <td>Entidade de Origem:
            <select id="filtro" name="origem">
                <option value="0">-- Padrão</option>
            </select>
        </td>
        <td><a href="<?=url::base()?>processes/new/" class="filterMenu"><img src="<?=url::site('images/actions/clock_add.png')?>" alt="Adicionar nova processo de medição" />&nbsp;&nbsp;&nbsp;Agendar novo processo de medição</a></td>
    </tr>
</table>

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
        <tr>
            <td><a href="<?=Url::site('entities/view').'/'.$entity->id?>"><?=$entity->name?></a></td>
            <td><?=$entity->ipaddress?></td>
            <td><?=$entity->state?> / <?=$entity->city?></td>
            <td><?=Date("Y-m-d H:i:s",$entity->updated)?></td>
            <td>
                <a href="<?=url::site('processes/edit').'/'.$process->id?>"><img src="<?=url::site('images/actions/clock_edit.png')?>" alt="Editar" /></a>
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