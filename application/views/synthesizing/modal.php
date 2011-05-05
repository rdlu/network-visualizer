<?php if($header): ?>
<table class="filterMenu">
	<tr>
		<td>Origem: <?=$source->name?> (<?=$source->ipaddress?>)</td>
		<td>Destino: <?=$destination->name?> (<?=$destination->ipaddress?>)</td>
		<td>
			<select name="relative" id="relativePeriod">
				<option value="7 days">Últimos 7 dias</option>
				<option value="1 month">Último mês</option>
				<option value="24 hours">Últimas 24 horas</option>
			</select>
		</td>
	</tr>
</table>
<br />
<div id="tabs">
<?php endif; ?>
	<div id="metricsAccordion" class="accordion">
		<?php foreach($metrics as $name => $properties): ?>
		<h3><a href="#"><?=$properties['desc']?> (<?=$name?>)</a></h3>
		<div><?php foreach($images[$name] as $image): ?>
			<img src="<?=url::site().$image?>?<?=date("Ymdhis")?>" alt="<?=$name?>" style="min-height:280px"/>
		<?php endforeach; ?></div>
		<?php endforeach; ?>
	</div>
<?php if($header): ?>
</div>
<script>
	$(function() {
		$("#metricsAccordion").accordion({
			collapsible: true,
			active: <?=count($images)-1 ?>,
			fillSpace: true,
			clearStyle: true
		});

		jQuery("#relativePeriod").change( function() {
			var period = $(this).val();
			jQuery.ajax({
				url: "<?=url::site('synthesizing/modal')?>",
				type: 'post',
				data: {
					source: <?=$source->id?>,
					destination: <?=$destination->id?>,
					relative: period
				},
				beforeSend: function() {
					jQuery('#tabs').addClass('loadingBig');
				},
				complete: function() {
					jQuery('#tabs').removeClass('loadingBig');
				},
				success: function(data) {
					jQuery("#tabs").html(data);
				},
				error: function(status, msg, error) {
					console.log(error);
				}
			});
		});
	});
</script>
<?php endif; ?>