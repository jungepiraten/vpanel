<ul class="entrylist">
{foreach from=$dokumente item=dokument}
<li class="entry{cycle values="odd,even"}">
<div style="float:left; margin-left:10px;"><a href="{"dokumente_details"|___:$dokument.dokumentid}">{$dokument.label}</a><br>
<span class="description">{$dokument.file.mimetype}</span></div>
<div style="clear:both;"></div>
</li>
{/foreach}
</ul>
