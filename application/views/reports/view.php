<div id="tabs">
	<div id="metricsAccordion" class="accordion">
		<?php foreach($metrics as $name => $properties): ?>
		<h3><a href="#"><?=$properties['desc']?> (<?=$name?>)</a></h3>
		<div><?php foreach($images[$name] as $image): ?>
			<img src="<?=$image?>?<?=date("Ymdhis")?>" alt="<?=$name?>" style="min-height:280px"/>
		<?php endforeach; ?></div>
		<?php endforeach; ?>
	</div>
</div>
<script>
	$(function() {
		$("#metricsAccordion").accordion({
			collapsible: true,
			active: <?=count($images)-1 ?>,
			fillSpace: true
		});
	});
</script>
