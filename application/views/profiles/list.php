<table id="filterMenu">
    <tr>
        <td>Filtro <select id="das"></select></td>
        <td><a href="<?=url::base()?>profiles/new/" class="filterMenu"><img src="<?=url::site('images/actions/add.png')?>" alt="Adicionar novo perfil" />&nbsp;&nbsp;&nbsp;Novo Perfil de Teste</a></td>
    </tr>
</table>
<table id="entityList" class="tablesorter">
    <thead>
    <tr>
        <th>Nome do perfil</th>
        <th>Métricas cobertas</th>
        <th>Status</th>
        <th>Ações</th>
    </tr>
    </thead>
<tbody>
<?php if(count($profiles) > 0): ?>
    <?php foreach($profiles as $profile): ?>
        <tr>
            <td><a href="<?=Url::site('profiles/view').'/'.$profile->id?>"><?=$profile->name?></a></td>
            <td><?php foreach($profile->metrics as $metric): ?>
                    <?=$metric->name?> 
                <?php endforeach; ?>
             </td>
            <td><?=$profile->status?></td>
            <td>
                <a href="<?=url::site('profiles/remove').'/'.$profile->id?>"><img src="<?=url::site('images/actions/delete.png')?>" alt="Remover" /></a>
            </td>
        </tr>
    <? endforeach ?>
<?php else: ?>
<tr>
    <td colspan="4">Nenhum perfil encontrado</td>
</tr>
<?php endif ?>
        </tbody>
</table>
