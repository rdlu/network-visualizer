<table id="filterMenu">
    <tr>
        <td><a href="<?=url::base()?>entities/new/" class="filterMenu"><img src="<?=url::site('images/actions/computer_add.png')?>" alt="Adicionar nova entidade" />&nbsp;&nbsp;&nbsp;Cadastrar Nova Entidade</a></td>
    </tr>
</table>

<table id="entityList" class="tablesorter">
    <thead>
    <tr>
        <th>Nome da entidade</th>
        <th>Endereço IP / Hostname DDNS</th>
        <th>UF / Cidade</th>
        <th>Ult. Atualização</th>
	    <th>Status</th>
    </tr>
    </thead>
<tbody>
<?php if(count($entities) > 0): ?>
    <?php foreach($entities as $entity): ?>
        <tr>
            <td><a href="<?=Url::site('entities/view').'/'.$entity->id?>"><?=$entity->name?></a></td>
            <td><?=$entity->ipaddress?></td>
            <td><?=$entity->state?> / <?=$entity->city?></td>
            <td><?=Date("Y-m-d H:i:s",$entity->updated)?></td>
            <td><?=Sonda::instance($entity->id)->getString()?></td>
        </tr>

    <? endforeach ?>

<?php else: ?>
<tr>
    <td colspan="4">Nenhuma entidade encontrada</td>
</tr>
<?php endif ?>
    </tbody>
</table>
<script type="text/javascript">
$(function(){
	$('#entityList').tablesorter({
		'headers': {
			4: {sorter: false}
		},
		'widgets': ['zebra']
	});
});
</script>