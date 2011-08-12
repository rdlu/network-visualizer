<table id="filterMenu">
    <tr>
        <td style="text-align:left"><a href="<?=url::base()?>entities/" class="filterMenu"><img src="<?=url::site('images/actions/arrow_left.png')?>" alt="Adicionar nova entidade" />&nbsp;&nbsp;&nbsp;Voltar à listagem</a></td>
    </tr>
</table>

<form action="#" class="bForms">
	<fieldset>
		<legend>Informações básicas</legend>
		<span class="iblock">Nome da entidade: <strong><?=$entity->name?></strong> (<?=$entity->id?>)</span>
		<span class="iblock">Endereço IP: <strong><?=$entity->ipaddress?></strong></span>
		<span class="iblock"><span class="label">Cidade: </span><strong><?=$entity->city?></strong></span>
		<span class="iblock"><span class="label">UF: </span><strong><?=$entity->state?></strong></span>
	</fieldset>
	<fieldset>
		<legend>Status</legend>
		<span class="iblock status <?=$status->getClass()?>"><?=$status->getMessage()?></span>
	</fieldset>
	<fieldset>
		<legend><strong>Papel Gerente</strong> (dispara medições contra):</legend>
		<ul>
			<?php if(count($destinations) == 0): ?>
			<li class="noresults">Esta sonda não tem o papel de gerente.</li>
			<?php else: foreach($destinations as $destination): ?>
			<li><a href="<?=Url::site('entities/view').'/'.$destination['id']?>"><?=$destination['name']?> (<?=$destination['ipaddress']?>)</a></li>
			<?php endforeach; endif;  ?>
		</ul>
	</fieldset>
	<fieldset>
		<legend><strong>Papel Agente</strong> (recebe medições de):</legend>
		<ul>
			<?php if(count($sources) == 0): ?>
			<li>Esta sonda não tem o papel de agente.</li>
			<?php else: foreach($sources as $source): ?>
			<li><a href="<?=Url::site('entities/view').'/'.$source['id']?>"><?=$source['name']?> (<?=$source['ipaddress']?>)</a></li>
			<?php endforeach; endif;  ?>
		</ul>
	</fieldset>
</form>
<script type="text/javascript">
	var processes = <?=$procJSON?>;
	var sources = <?=Zend_Json::encode($sources)?>;
	var destinations = <?=Zend_Json::encode($destinations)?>;
</script>