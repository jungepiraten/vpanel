<table class="table table-striped table-bordered table-condensed">
	<thead>
		<tr>
			<th>#</th>
			<th>Name</th>
		</tr>
	</thead>
	{foreach from=$mitglieder item=mitglied}
		<tr onclick="doNav('{"mitglieder_details"|___:$mitglied.mitgliedid}');" {if isset($mitglied.austritt)}id="ex"{/if}>
			<td>{$mitglied.mitgliedid|escape:html}</td>
			<td>
				{if isset($mitglied.austritt)}
				<span class="label label-important" style="float:right;">Ausgetreten</span>
				{/if}
				{if $showmitglieddokumentdel and isset($dokument)}
				<a href="{"mitglieddokument_delete"|___:$mitglied.mitgliedid:$dokument.dokumentid}" class="close">&times;</a>
				{/if}
				{include file=mitgliederlist.item.tpl mitglied=$mitglied}
			</td>
		</tr>
	{/foreach}
</table>
