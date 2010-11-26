<ul class="entrylist">
{foreach from=$mitglieder item=mitglied}
<li class="entry{cycle values="odd,even"}" {if isset($mitglied.austritt)}id="ex"{/if}>
{if $showmitglieddel and ! isset($mitglied.austritt)}
<div style="float:left; padding-top:7px;">
 <a href="{"mitglieder_del"|___:$mitglied.mitgliedid}" class="delimg" title="Mitglied entfernen" onClick="return confirm('Mitglied wirklich löschen?');">&nbsp;</a>
</div>{/if}
<div style="width: 2px; height: 30px; background-color: #404040; float:left; margin-left:{if isset($mitglied.austritt)}25{else}10{/if}px;"></div>
<div style="float:left; margin-left:10px;"><a href="{"mitglieder_details"|___:$mitglied.mitgliedid}">
{if isset($mitglied.latest.natperson)}{$mitglied.latest.natperson.name|escape:html}, {$mitglied.latest.natperson.vorname|escape:html}{/if}
{if isset($mitglied.latest.jurperson)}{$mitglied.latest.jurperson.label|escape:html}{/if}
</a><br>
<span class="description">{$mitglied.latest.kontakt.strasse} {$mitglied.latest.kontakt.hausnummer}, {$mitglied.latest.kontakt.ort.plz} {$mitglied.latest.kontakt.ort.label}</span></div>
<div style="clear:both;"></div>
</li>
{/foreach}
</ul>
