{include file="header.html.tpl" ansicht="Mitgliederstatistik" menupunkt="stats"}
<p>Insgesamt <strong>{$mitgliedercount}</strong> Mitglieder.</p>

<h2>Aufteilung nach Mitgliedschaften</h2>
<table class="table table-striped">
	{foreach from=$mitgliedschaften item=mitgliedschaft}
		{assign var=mitgliedschaftid value=$mitgliedschaft.mitgliedschaftid}
		<tr>
			<td>{$mitgliedercountPerMitgliedschaft.$mitgliedschaftid}</td>
			<td>{$mitgliedercountPerMitgliedschaft.$mitgliedschaftid/$mitgliedercount*100|string_format:"%.2f"} %</td>
			<th>{$mitgliedschaft.label}</th>
		</tr>
	{/foreach}
</table>

<h2>Aufteilung nach Staaten</h2>
<table class="table table-striped">
	{foreach from=$states item=state}
		{assign var=stateid value=$state.stateid}
		<tr>
			<td>{$mitgliedercountPerState.$stateid}</td>
			<td>{$mitgliedercountPerState.$stateid/$mitgliedercount*100|string_format:"%.2f"} %</td>
			<td>{if $state.population != null}{$mitgliedercountPerState.$stateid/$state.population*1000*1000|string_format:"%.2f"} pro Mio{/if}</td>
			<th>{$state.label}</th>
		</tr>
	{/foreach}
</table>
{include file="footer.html.tpl"}
