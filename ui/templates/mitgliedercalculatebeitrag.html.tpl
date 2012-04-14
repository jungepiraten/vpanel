{include file="header.html.tpl" ansicht="Mitgliederbeitragsverteilung" menupunkt="mitglied"}
<table class="table table-striped">
	<thead>
		<tr>
			<th>Gliederung</th>
			<th>Anteil</th>
			<th>Aktuell</th>
			<th>Balanciert</th>
		</tr>
	</thead>
	{assign var=sumAnteil value=0}
	{assign var=sumGliederung value=0}
	{assign var=sumWunsch value=0}
	{foreach from=$gliederungen item=gliederung}
		{assign var=gliederungid value=$gliederung.gliederungid}
		{math assign=sumAnteil equation="sum + anteil" sum=$sumAnteil anteil=$anteile.$gliederungid}
		{math assign=sumGliederung equation="sum + anteil" sum=$sumGliederung anteil=$gliederungshoehe.$gliederungid}
		{math assign=sumWunsch equation="sum + anteil" sum=$sumWunsch anteil=$wunschhoehe.$gliederungid}
		<tr>
			<th>{$gliederung.label|escape:html}</th>
			<td>{$anteile.$gliederungid*100|string_format:"%.3f"} %</td>
			<td>{$gliederungshoehe.$gliederungid|string_format:"%.2f"} EUR</td>
			<td>{$wunschhoehe.$gliederungid|string_format:"%.2f"} EUR</td>
		</tr>
	{/foreach}
	<tfoot>
		<tr>
			<th>Summe</th>
			<th>{$sumAnteil*100|string_format:"%.3f"} %</th>
			<th>{$sumGliederung|string_format:"%.2f"} EUR</th>
			<th>{$sumWunsch|string_format:"%.2f"} EUR</th>
		</tr>
	</tfoot>
</table>
{include file="footer.html.tpl"}
