<?php if(count($profiles) > 0): ?>
    <ul>
    <?php foreach($profiles as $profile): ?>
        <li><a href="<?=url::base()?>profiles/edit/<?=$profile->id?>"><?=$profile->name?></a></li>
    <? endforeach ?>
    </ul>
<?php else: ?>
        Nenhum perfil encontrado
<?php endif ?>
<h2><a href="<?=url::base()?>profiles/new/">Novo Perfil de Medição</a></h2>
