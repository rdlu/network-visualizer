<h2><a href="<?=url::base().Request::instance()->controller?>">Voltar</a></h2>
<?php if($errors): ?>
<ul>
    <?php foreach($errors as $error): ?>
    <li><?=$error?></li>
    <?php endforeach; ?>
</ul>
<?php endif ?>

<?=Form::open(Request::instance()->controller.'/'.Request::instance()->action.'/'.Request::instance()->param('id',0),array('id'=>'newEntity'))?>
<dl>
<?php foreach ($entity->inputs() as $label => $input): ?>
    <dt><?php echo $label ?></dt>
    <dd><?php echo $input ?></dd>
<?php endforeach ?>
    <dd>&nbsp;</dd>
    <dt><?=Form::submit('submit_entity','OK')?></dt>
</dl>
<?=Form::close()?>