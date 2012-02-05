<ul class="entrylist">
{foreach from=$mitglieder item=mitglied}
<li class="entry{cycle values="odd,even"}" {if isset($mitglied.austritt)}id="ex"{/if}>
{if $showmitglieddel and ! isset($mitglied.austritt)}
<div style="float:right; margin-top:7px;margin-right:7px;">
 <a href="{"mitglieder_del"|___:$mitglied.mitgliedid}" class="delimg" title="{"Mitglied entfernen"|__}" onClick="return confirm('{"Mitglied wirklich löschen?"|__}');">&nbsp;</a>
</div>{/if}
<div style="float:left; margin-left:10px;"><a href="{"mitglieder_details"|___:$mitglied.mitgliedid}">
{if isset($mitglied.latest.natperson)}{$mitglied.latest.natperson.name|escape:html}, {$mitglied.latest.natperson.vorname|escape:html}{/if}
{if isset($mitglied.latest.jurperson)}{$mitglied.latest.jurperson.label|escape:html}{/if}
</a><br>
<span class="description">{$mitglied.latest.kontakt.strasse} {$mitglied.latest.kontakt.hausnummer}, {$mitglied.latest.kontakt.ort.plz} {$mitglied.latest.kontakt.ort.label}</span></div>
<div style="clear:both;"></div>
</li>
{/foreach}
</ul>
