<?php echo form::open('account/signin') ?>
<div id="signin" style="text-align:center" class="centerBox">
	<strong>Bem Vindo ao NetMetric MoM</strong><br/><br/>
	<img src="<?=url::base();?>images/vivo.png" alt="Vivo Logo"/>
	<img src="<?=url::base();?>images/logo.png" alt="NetMetric MoM Logo"/><br/>
	<strong><?php echo form::label('username', 'Usuário') ?></strong> <?php echo form::input('username', '', array('id' => 'username', 'class' => 'nice big')) ?>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<strong><?php echo form::label('password', 'Senha') ?></strong> <?php echo form::password('password', '', array('id' => 'password', 'class' => 'nice big')) ?>
	<?php echo form::submit('submit', 'Entrar', array('class' => 'nice')) ?><br />
	<?php if (isset($errors)): ?>
	<ul class="errors">
		<?php foreach($errors as $error): ?>
		<li class="<?=$error['class']?>"><?=$error['message']?></li>
		<?php endforeach; ?>
	</ul><br />
	<?php endif; ?>
</div>
<?php echo form::close() ?>