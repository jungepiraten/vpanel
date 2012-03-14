{include file="header.html.tpl" ansicht="Mitgliederstatistik" menupunkt="statistik"}
<p>Insgesamt <strong>{$mitgliedercount}</strong> Mitglieder.</p>
<h2>Aufteilung nach Mitgliedschaften</h2>
<table class="table table-striped">
{foreach from=$mitgliedercountPerMitgliedschaft item=mitgliedschaft}
<tr>
 <td>{$mitgliedschaft.count}</td>
 <td>{$mitgliedschaft.count/$mitgliedercount*100|string_format:"%.2f"} %</td>
 <th>{$mitgliedschaft.label}</th>
</tr>
{/foreach}
</table>
<h2>Aufteilung nach Staaten</h2>
<table class="table table-striped">
{foreach from=$mitgliedercountPerState item=state}
<tr>
 <td>{$state.count}</td>
 <td>{$state.count/$mitgliedercount*100|string_format:"%.2f"} %</td>
 <td>{if $state.population != null}{$state.count/$state.population*1000*1000|string_format:"%.2f"} pro Mio{/if}</td>
 <th>{$state.label}</th>
</tr>
{/foreach}
</table>
{include file="footer.html.tpl"}
