<h2><a href="<?=url::base()?>profiles/">Voltar</a></h2>
<?php if($errors): ?>
<ul>
    <?php foreach($errors as $error): ?>
    <li><?=$error?></li>
    <?php endforeach; ?>
</ul>
<?php endif ?>

<?=Form::open('profiles/new',array('id'=>'newProfile'))?>
<dl>
<?php foreach ($profile->inputs() as $label => $input): ?>
    <dt><?php echo $label ?></dt>
    <dd><?php echo $input ?></dd>
<?php endforeach ?>
    <dd>&nbsp;</dd>
    <dt><?=Form::submit('submit_entity','OK')?></dt>
</dl>
<?=Form::close()?>