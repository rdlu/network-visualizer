<table id="filterMenu">
    <tr>
        <td>
            <a href="<?=url::base().Request::instance()->controller?>" class="filterMenu">Voltar</a>
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

<?=Form::open(Request::instance()->controller.'/'.Request::instance()->action.'/'.Request::instance()->param('id',0),array('id'=>'newProfile','class'=>'bForms'))?>
<fieldset title="Dados Obrigatórios">
    <legend>Dados Obrigatórios</legend>
    <?=$profile->label('name');?>
    <?=$profile->input('name',array('id'=>'name'));?><br />
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
        <?=$profile->label('status');?>
    <?=$profile->input('status',array('id'=>'status'));?>
    <?=$profile->input('metrics',array('id'=>'metrics'));?>
</fieldset>
    <?=Form::submit('submit_'.Request::instance()->controller,'OK')?>
<?=Form::close()?>