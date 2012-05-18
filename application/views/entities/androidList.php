
<table id="entityList" class="tablesorter">
    <thead>
    <tr>
        <th>Nome da entidade</th>
        <th>Endereço IP / Hostname DDNS</th>
        <th>UF / Cidade</th>
        <th>Ult. Atualização</th>
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
        </tr>

            <? endforeach ?>

        <?php else: ?>
    <tr>
        <td colspan="3">Nenhuma entidade encontrada</td>
    </tr>
        <?php endif ?>
    </tbody>
</table>
<script type="text/javascript">
    $(function(){
        $('#entityList').tablesorter({
            'widgets': ['zebra']
        });
    });
</script>