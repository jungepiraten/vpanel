<a href="{"mitglieder_details"|___:$mitglied.mitgliedid}" {if $mitglied.austritt}style="text-decoration:line-through;"{/if}>
	{if isset($mitglied.latest.natperson)}{$mitglied.latest.natperson.name|escape:html}, {$mitglied.latest.natperson.vorname|escape:html}{/if}
	{if isset($mitglied.latest.jurperson)}{$mitglied.latest.jurperson.label|escape:html}{/if}
</a><br />
<span class="description">{$mitglied.latest.kontakt.strasse} {$mitglied.latest.kontakt.hausnummer}, {$mitglied.latest.kontakt.ort.plz} {$mitglied.latest.kontakt.ort.label}</span>
