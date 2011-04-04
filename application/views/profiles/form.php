<table id="filterMenu">
    <tr>
        <td>
            <a href="<?=url::base().Request::current()->controller()?>" class="filterMenu">Voltar</a>
        </td>
        <td>
            <i>Cadastro de Novo Perfil</i>
        </td>
    </tr>
</table>
<?php if($errors): ?>
<ul class="errors">
    <?php foreach($errors as $error): ?>
    <li class="error"><?=$error?></li>
    <?php endforeach; ?>
</ul>
<?php endif ?>

<?=Form::open(Request::current()->controller().'/'.Request::current()->action().'/'.Request::current()->param('id',0),array('id'=>'newProfile','class'=>'bForms'))?>
<fieldset title="Dados Obrigatórios">
    <legend>Dados Obrigatórios</legend>
    <?=$profile->label('name');?>
    <?=$profile->input('name',array('id'=>'name'));?>
	 <?=$profile->label('protocol');?>
    <?=$profile->input('protocol',array('id'=>'protocol'));?><br />
    <?=$profile->label('polling');?>
    <?=$profile->input('polling',array('id'=>'polling'));?>
    <?=$profile->label('count');?>
    <?=$profile->input('count',array('id'=>'count'));?><br />
    <?=$profile->label('probeCount');?>
    <?=$profile->input('probeCount',array('id'=>'probeCount'));?>
    <?=$profile->label('probeSize');?>
    <?=$profile->input('probeSize',array('id'=>'probeSize'));?><br />
    <?=$profile->label('gap');?>
    <?=$profile->input('gap',array('id'=>'gap'));?>
    <?=$profile->label('timeout');?>
    <?=$profile->input('timeout',array('id'=>'timeout'));?><br />
    <?=$profile->label('qosType');?>
    <?=$profile->input('qosType',array('id'=>'qosType'));?>
    <?=$profile->label('qosValue');?>
    <?=$profile->input('qosValue',array('id'=>'qosValue'));?><br />
	<?=$profile->label('description');?>
    <?=$profile->input('description',array('id'=>'description', 'rows'=>2, 'style'=>'vertical-align:baseline;'));?><br />
    <fieldset class="group left">
        <legend>Métricas cobertas por este perfil</legend>
        <?=$profile->input('metrics');?>
    </fieldset>
	Status:&nbsp;
    <?=$profile->input('status',array('id'=>'status'));?>

</fieldset>
    <?=Form::submit('submit_'.Request::current()->controller(),'OK')?>
<?=Form::close()?>