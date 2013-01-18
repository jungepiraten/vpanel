<table class="table table-striped table-condensed table-bordered">
	<thead>
		<tr>
			<th>#</th>
			<th>Name</th>
		</tr>
	</thead>
	{foreach from=$beitraege item=beitrag}
		<tr onclick="doNav('{"beitraege_details"|___:$beitrag.beitragid}')">
			<td>{$beitrag.beitragid}</td>
			<td>
				<a href="{"beitraege_details"|___:$beitrag.beitragid}">{$beitrag.label}</a>
				<a class="close delete"  href="{"beitraege_del"|___:$beitrag.beitragid}">&times;</a>
			</td>
		</tr>
	{/foreach}
</table>
