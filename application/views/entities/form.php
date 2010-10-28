<h2><a href="<?=url::base()?>entities/">Voltar</a></h2>
<?php if($errors): ?>
<ul>
    <?php foreach($errors as $error): ?>
    <li><?=$error?></li>
    <?php endforeach; ?>
</ul>
<?php endif ?>

<?=Form::open('entities/new',array('id'=>'newEntity'))?>
<dl>
<?php foreach ($entity->inputs() as $label => $input): ?>
    <dt><?php echo $label ?></dt>
    <dd><?php echo $input ?></dd>
<?php endforeach ?>
    <dd>&nbsp;</dd>
    <dt><?=Form::submit('submit_entity','OK')?></dt>
</dl>
<?=Form::close()?>