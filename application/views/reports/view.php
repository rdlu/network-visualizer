<div id="tabs">
	<div id="metricsAccordion" class="accordion">
		<?php foreach($metrics as $order => $metric): ?>
		<h3><a href="#"><?=$metric->desc?> (<?=$metric->name?>)</a></h3>
		<div id="<?=$metric->name?>-area">
            <div id="img-<?=$metric->name?>" style="width: 900px; margin-right: 5px; float: left">
            <?php foreach($images[$metric->name] as $image): ?>
			    <img src="<?=$image?>?<?=date("Ymdhis")?>" alt="<?=$metric->name?>" style="min-height:280px"/>
		    <?php endforeach; ?>
            </div>
        </div>
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
