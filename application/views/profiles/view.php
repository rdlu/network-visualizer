<table id="filterMenu">
    <tr>
        <td style="text-align:left"><a href="<?=url::base()?>entities/" class="filterMenu"><img src="<?=url::site('images/actions/arrow_left.png')?>" alt="Adicionar nova entidade" />&nbsp;&nbsp;&nbsp;Voltar à listagem</a></td>
    </tr>
</table>

<form action="#" class="bForms">
	<fieldset>
		<legend>Informações básicas</legend>
		<span class="iblock">Nome do perfil: <strong><?=$profile->name?></strong> (<?=$profile->id?>)</span>
		<span class="iblock">Protocolo: <strong><?=$profile->protocol?></strong></span><br />
		<span class="iblock"><span class="label">Cidade: </span><strong><?=$entity->city?></strong></span>
		<span class="iblock"><span class="label">UF: </span><strong><?=$entity->state?></strong></span>
	</fieldset>
	<fieldset>
		<legend>Status</legend>
	</fieldset>
</form>