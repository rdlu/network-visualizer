<div id="tabs">
	<div id="metricsAccordion" class="accordion">
		<?php foreach($metrics as $order => $metric): ?>
		<h3><a href="#"><?=$metric->desc?> (<?=$metric->name?>)</a></h3>
		<div><?php foreach($images[$metric->name] as $image): ?>
			<img src="<?=$image?>?<?=date("Ymdhis")?>" alt="<?=$metric->name?>" style="min-height:280px"/>
		<?php endforeach; ?></div>
		<?php endforeach; ?>
	</div>
</div>
<script>
	$(function() {
		$("#metricsAccordion").accordion({
			collapsible: true,
			active: <?=count($images)-1 ?>,
			fillSpace: true,
			clearStyle: true
		});
	});
</script>
