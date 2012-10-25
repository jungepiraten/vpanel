{include file="header.html.tpl" ansicht="Mitgliederbeitragsverteilung" menupunkt="mitglied"}
<table class="table table-striped">
	<thead>
		<tr>
			<th>Gliederung</th>
			<th>Ist-Betrag</th>
			<th>Soll-Betrag</th>
		</tr>
	</thead>
	{assign var=sumIst value=0}
	{assign var=sumSoll value=0}
	{foreach from=$gliederungen item=gliederung}
		{assign var=gliederungid value=$gliederung.gliederungid}
		{math assign=sumIst equation="sum + anteil" sum=$sumIst anteil=$istHoehe.$gliederungid}
		{math assign=sumSoll equation="sum + anteil" sum=$sumSoll anteil=$sollHoehe.$gliederungid}
		<tr>
			<th>{$gliederung.label|escape:html}</th>
			<td>{$istHoehe.$gliederungid|string_format:"%.2f"} EUR</td>
			<td>{$sollHoehe.$gliederungid|string_format:"%.2f"} EUR</td>
		</tr>
	{/foreach}
	<tfoot>
		<tr>
			<th>Summe</th>
			<th>{$sumIst|string_format:"%.2f"} EUR</th>
			<th>{$sumSoll|string_format:"%.2f"} EUR</th>
		</tr>
	</tfoot>
</table>
{include file="footer.html.tpl"}
