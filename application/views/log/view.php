<script type="text/javascript">
$(function(){
	$('#log_table').tablesorter({
		'headers': {
			2: {sorter: false},
			3: {sorter: false},
			4: {sorter: false},
			5: {sorter: false},
			6: {sorter: false},
			7: {sorter: false},
			8: {sorter: false}
		},
		'widgets': ['zebra']
	});
})
</script>
<style type="text/css">
table#log_table td.error { background: #f96; }
table#log_table td.info { background: #9cf; }
table#log_table td.warning { background: #fff12d; }
</style>

<h1>Logs do dia <?=date('d/m/Y')?></h1>
<table id="log_table" class="tablesorter" border="0" cellpadding="0" cellspacing="1">
	<thead>
		<tr>
			<th>time</th>
			<th>type</th>
			<th>message</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($logs as $log): ?>
		<tr>
			<td><?php echo HTML::chars($log['time']) ?></td>
			<td class="<?php echo HTML::chars(strtolower($log['type'])) ?>"><?php echo HTML::chars($log['type']) ?></td>
			<td><?php echo HTML::chars($log['message']) ?></td>
		</tr>
	<?php endforeach ?>
</tbody>
</table>
