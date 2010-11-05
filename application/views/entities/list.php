<table>
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
            <td><?=$entity->serverName?>(<?=$entity->ipaddress?>)</td>
            <td><?=$entity->state?>/<?=$entity->city?></td>
            <td><?=Date("Y-m-d H:i:s",$entity->updated)?></td>
            <td>
                <a href="<?=url::site('entities/edit').'/'.$entity->id?>"><img src="<?=url::site('img/actions/edit.png')?>" alt="Editar" /></a>
                <a href="<?=url::site('entities/remove').'/'.$entity->id?>"><img src="<?=url::site('img/actions/remove.png')?>" alt="Remover" /></a>
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
<h2><a href="<?=url::base()?>entities/new/">Nova Entidade</a></h2>
