<ul class="entrylist">
{foreach from=$mitglieder item=mitglied}
<li class="entry{cycle values="odd,even"}" {if isset($mitglied.austritt)}id="ex"{/if}>
{if $showmitglieddel and ! isset($mitglied.austritt)}
<a style="float:right; margin-top:7px;margin-right:7px;"href="{"mitglieder_del"|___:$mitglied.filterid}" class="delimg" title="{"Mitglied entfernen"|__}" onClick="return confirm('{"Mitglied wirklich löschen?"|__}');">&nbsp;</a>
{/if}
{if $showmitglieddokumentdel and isset($dokument)}
<a style="float:right; margin-top:7px;margin-right:7px;"href="{"mitglieddokument_delete"|___:$mitglied.mitgliedid:$dokument.dokumentid}" class="delimg" title="{"Zuordnung entfernen"|__}">&nbsp;</a>
{/if}
{include file=mitgliederlist.item.tpl mitglied=$mitglied}
<div style="clear:both;"></div>
</li>
{/foreach}
</ul>
