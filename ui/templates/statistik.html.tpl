{include file="header.html.tpl" ansicht="Mitgliederstatistik"}
<h1 class="pagetitle">Mitgliederstatistik</h1>
<p>Insgesamt <strong>{$mitgliedercount}</strong> Mitglieder.</p>
<h2>Aufteilung nach Mitgliedschaften</h2>
<table>
{foreach from=$mitgliedercountPerMitgliedschaft item=mitgliedschaft}
<tr>
 <td class="count">{$mitgliedschaft.count}</td>
 <td class="percent">{$mitgliedschaft.count/$mitgliedercount*100|string_format:"%.2f"} %</td>
 <th class="label">{$mitgliedschaft.label}</th>
</tr>
{/foreach}
</table>
<h2>Aufteilung nach Staaten</h2>
<table>
{foreach from=$mitgliedercountPerState item=state}
<tr>
 <td class="count">{$state.count}</td>
 <td class="percent">{$state.count/$mitgliedercount*100|string_format:"%.2f"} %</td>
 <th class="label">{$state.label}</th>
</tr>
{/foreach}
</table>
{include file="footer.html.tpl"}
