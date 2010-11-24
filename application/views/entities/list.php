<table id="filterMenu">
    <tr>
        <td>Filtro <select id="das"></select></td>
        <td><a href="<?=url::base()?>entities/new/" class="filterMenu"><img src="<?=url::site('images/actions/computer_add.png')?>" alt="Adicionar nova entidade" />&nbsp;&nbsp;&nbsp;Cadastrar Nova Entidade</a></td>
    </tr>
</table>

<table id="entityList" class="tablesorter">
    <thead>
    <tr>
        <th>Nome da entidade</th>
        <th>Endereço IPv4</th>
        <th>UF / Cidade</th>
        <th>Ult. Atualização</th>
        <th>Ações</th>
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
            <td>
                <a href="<?=url::site('entities/edit').'/'.$entity->id?>"><img src="<?=url::site('images/actions/computer_edit.png')?>" alt="Editar" /></a>
                <a href="<?=url::site('entities/remove').'/'.$entity->id?>"><img src="<?=url::site('images/actions/computer_delete.png')?>" alt="Remover" /></a>
            </td>
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
			4: {sorter: false},
		},
		'widgets': ['zebra']
	});
});
</script>