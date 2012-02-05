<ul class="entrylist">
{foreach from=$dokumente item=dokument}
<li class="entry{cycle values="odd,even"}">
{if $showmitglieddokumentdel && isset($mitglied)}
 <a style="float:right; margin-top:7px;margin-right:7px;" href="{"mitglieddokument_delete"|___:$mitglied.mitgliedid:$dokument.dokumentid}" class="delimg">&nbsp;</a>
{/if}
<div style="float:left; margin-left:10px;"><a href="{"dokumente_details"|___:$dokument.dokumentid}">{$dokument.label}</a><br>
<span class="description">{$dokument.file.mimetype}</span></div>
<div style="clear:both;"></div>
</li>
{/foreach}
</ul>
