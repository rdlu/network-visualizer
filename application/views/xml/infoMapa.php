<?="<?xml version='1.0' encoding='utf-8'?>\n" ?>
<xml>
	<entities>
<?php foreach($entities as $k => $entity): ?>
		<sonda>
			<id><?=$entity->id?></id>
			<ip><?=$entity->ipaddress?></ip>
			<nome><?=$entity->name?></nome>
			<status>1</status>
			<latitude><?=$entity->latitude?></latitude>
			<longitude><?=$entity->longitude?></longitude>
			<agentes>
<?php foreach($entity->processes_as_source as $process): ?>
				<med><?=$process->destination->id?></med>
<?php endforeach; ?>
			</agentes>
		</sonda>
<?php endforeach; ?>
	</entities>
</xml>