<div id="tabs">
	<ul>
<?php foreach($processes as $process): ?>
<?php $process->profile->load(); ?>
		<li><a href="#perfil<?=$process->profile->id?>"><?=$process->profile->name?></a></li>
<?php endforeach; ?>
	</ul>
	<div id="metricsAccordion" class="accordion">
		<?php foreach($metrics as $metric): ?>
		<h3><a href="#"><?=$metric->name?> (<?=$metric->desc?>)</a></h3>
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
			active: <?=count($img)-1 ?>,
			fillSpace: true
		});
	});
</script>
