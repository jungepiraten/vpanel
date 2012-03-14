{if isset($mitglied.latest.natperson)}{$mitglied.latest.natperson.name|escape:html}, {$mitglied.latest.natperson.vorname|escape:html}{/if}
{if isset($mitglied.latest.jurperson)}{$mitglied.latest.jurperson.label|escape:html}{/if}
<br>
<span class="description">{$mitglied.latest.kontakt.strasse} {$mitglied.latest.kontakt.hausnummer}, {$mitglied.latest.kontakt.ort.plz} {$mitglied.latest.kontakt.ort.label}</span>
