<table id="filterMenu">
    <tr>
        <td><strong>Perfis de Teste</strong></td>
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
    <?php if (count($profiles) > 0): ?>
        <?php foreach ($profiles as $profile): ?>
        <tr>
            <td><a href="<?=Url::site('profiles/view') . '/' . $profile->id?>"><?=$profile->name?></a></td>
            <td><?php foreach ($profile->metrics->find_all() as $metric): ?>
                <?= $metric->name ?>
                <?php endforeach; ?>
                <?php if (count($profile->metrics) == 0): ?>
                    Este perfil não possui nenhuma métrica associada.
                    <?php endif ?>
            </td>
            <td><?=$profile->verbose('status')?></td>
            <td>
                <?php if (count($profile->metrics) == 0): ?>
                <a href="<?=url::site('profiles/remove') . '/' . $profile->id?>"><img
                    src="<?=url::site('images/actions/delete.png')?>" alt="Remover"/></a>
                <?php endif ?>
            </td>
        </tr>
            <? endforeach ?>
        <?php else: ?>
    <tr>
        <td colspan="4">Nenhum perfil encontrado, configure um perfil antes de configurar as métricas</td>
    </tr>
        <?php endif ?>
    </tbody>
</table>

<table class="filterMenu">
    <tr>
        <td><strong>Métricas</strong></td>
    </tr>
</table>

<table id="metricList" class="tablesorter">
    <thead>
    <tr>
        <th>Nome da métrica</th>
        <th>Descrição</th>
        <th>Perfil associado</th>
        <th>Ações</th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($metrics) > 0): ?>
        <?php foreach ($metrics as $metric): ?>
        <tr>
            <td><a href="<?=Url::site('profiles/view') . '/' . $metric->id?>"><?=$metric->name?></a></td>
            <td><?=$metric->desc?></td>
            <td><?php if ($metric->profile->id != 0): ?>
                <?= $metric->profile->name ?>
                <?php else: ?>
                Esta métrica não está associada à nenhum perfil.
                <?php endif ?>
            </td>
            <td>
                <?php if ($metric->profile->id == 0): ?>
                <a href="<?=url::site('metrics/remove') . '/' . $metric->id?>"><img
                    src="<?=url::site('images/actions/delete.png')?>" alt="Remover"/></a>
                <?php endif ?>
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
<script type="text/javascript">
    $(function () {
        $('#metricList').tablesorter({
            'headers':{
                3:{sorter:false}
            },
            'widgets':['zebra']
        });
    });
</script>