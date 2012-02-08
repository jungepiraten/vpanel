<ul class="entrylist">
{foreach from=$beitraege item=beitrag}
<li class="entry{cycle values="odd,even"}">
<a style="float:right; margin-top:7px;margin-right:7px;" href="{"beitraege_del"|___:$beitrag.beitragid}" class="delimg" title="{"Beitrag loeschen"|__}" onClick="return confirm('{"Beitrag wirklich löschen?"|__}');">&nbsp;</a>
<div style="float:left; margin-left:10px"><a href="{"beitraege_details"|___:$beitrag.beitragid}">{$beitrag.label}</a><br />
<span class="description">&nbsp;</span></div>
<div style="clear:both;"></div>
</li>
{/foreach}
</ul>
