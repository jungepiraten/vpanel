<ul class="entrylist">
{foreach from=$mitglieder item=mitglied}
<li class="entry{cycle values="odd,even"}">
<div style="float:left; padding-top:7px;">
{if $showmitglieddel}
 <a href="{"mitglied_del"|___:$mitglied.mitgliedid}" class="delimg" title="Mitglied entfernen" onClick="return confirm('Mitglied wirklich löschen?');">&nbsp;</a>
{/if}
</div>
<div style="width: 2px; height: 30px; background-color: #8f8f8f; float:left; margin-left:10px;"></div>
<div style="float:left; margin-left:10px;"><a href="{"mitglieder_details"|___:$mitglied.mitgliedid}" class="label">
{if isset($mitglied.latest.natperson)}{$mitglied.latest.natperson.name|escape:html}, {$mitglied.latest.natperson.vorname|escape:html}{/if}
{if isset($mitglied.latest.jurperson)}{$mitglied.latest.jurperson.label|escape:html}{/if}
</a><br>
<span class="description">{$mitglied.latest.kontakt.strasse} {$mitglied.latest.kontakt.hausnummer}, {$mitglied.latest.kontakt.ort.plz} {$mitglied.latest.kontakt.ort.label}</span></div>
<div style="clear:both;"></div>
</li>
{/foreach}
</ul>
