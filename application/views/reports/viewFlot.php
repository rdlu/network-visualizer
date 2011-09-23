<div id="tabs">
	<div id="metricsAccordion" class="accordion">
		<?php foreach($metrics as $order => $metric): ?>
		<h3><a href="#"><?=$metric->desc?> (<?=$metric->name?>)</a></h3>
		<div id="flot-<?=$metric->name?>"></div>
		<?php endforeach; ?>
	</div>
</div>
<script type="text/javascript">
	var results = <?=$results?>;

	$(function() {
		//jQuery.each

		$("#metricsAccordion").accordion({
			collapsible: true,
			active: <?=count($results)-1 ?>,
			fillSpace: true,
			clearStyle: true
		});
	});
</script>
