<?php if(count($entities) > 0): ?>
    <ul>
    <?php foreach($entities as $entity): ?>
        <li><a href="<?=url::base()?>entities/edit/<?=$entity->id?>"><?=$entity->name?></a></li>
    <? endforeach ?>
    </ul>
<?php else: ?>
        Nenhuma entidade encontrada
<?php endif ?>
<h2><a href="<?=url::base()?>entities/new/">Nova Entidade</a></h2>
