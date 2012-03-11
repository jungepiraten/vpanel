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
				
				{$beitrag.label}
					<span class="close" id="{$beitrag.beitragid}">&times;</span>
				<a class="delLink" style="display:none;" href="{"beitraege_del"|___:$beitrag.beitragid}">Soll der Beitrag wirklich gelöscht werden?</a>
				<p class="description">&nbsp;</p>
			</td>
		</tr>
	{/foreach}
</table>
{include file="deleteModal.block.tpl"}