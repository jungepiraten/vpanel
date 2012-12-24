<table class="table table-striped table-bordered table-condensed">
	<thead>
		<tr>
			<th>#</th>
			<th>Name</th>
		</tr>
	</thead>
	{foreach from=$dokumente item=dokument}
		<tr class="dokumentTr" onclick="doNav('{"dokumente_details"|___:$dokument.dokumentid}')">
			<td>{$dokument.latest.identifier|escape:html}</td>
			<td>
				{$dokument.latest.label|escape:html}
				{if $showmitglieddokumentdel && isset($mitglied)}
				<a href="{"mitglieddokument_delete"|___:$mitglied.mitgliedid:$dokument.dokumentid}" class="close">&times;</a>
				{/if}
				<span class="description">{$dokument.latest.file.mimetype|escape:html}, {$dokument.latest.file.filesize|file_size}</span></div>
			</td>
		</tr>
	{/foreach}
</table>
