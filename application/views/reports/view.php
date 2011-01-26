<div id="tabs">
	<ul>
<?php foreach($processes as $process): ?>
<?php $process->profile->load(); ?>
		<li><a href="#perfil<?=$process->profile->id?>"><?=$process->profile->name?></a></li>
<?php endforeach; ?>
	</ul>
<?php foreach($processes as $process): ?>
		<?php $process->profile->load(); ?>
	<div id="perfil<?=$process->profile->id?>">
		<span class="perfil descricao">Descrição do perfil: <?=$process->profile->description ?></span><br />
		<?php $metrics = $process->profile->metrics?>
		<div id="profile<?=$process->profile->id?>accordion" class="accordion">
			<?php foreach($metrics as $metric): ?>
			<h3><a href="#"><?=$metric->name?> (<?=$metric->desc?>)</a></h3>
			<div><?php foreach($images[$process->profile->id][$metric->name] as $image): ?>
				<img src="<?=$image?>" alt="<?=$metric->name?>" />
			<?php endforeach; ?></div>
			<?php endforeach; ?>
		</div>
	</div>
<?php endforeach; ?>
</div>
<script>
	$(function() {
		$("#tabs").tabs();
		<?php foreach($images as $k=>$img): ?>
			$("#profile<?=$k?>accordion").accordion({
				collapsible: true,
				active: <?=count($img)-1 ?>
			});
		<?php endforeach; ?>
	});
</script>
