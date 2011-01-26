{include file="header.html.tpl" ansicht="Mailvorlagen verwalten"}
<p class="pagetitle">{"Mailvorlagen verwalten"|__}</p>
<ul class="entrylist">
{foreach from=$mailtemplates item=template}
<li class="entry{cycle values="odd,even"}">
<div style="float:left; padding-top:7px;">
 <a href="{"mailtemplates_del"|___:$template.templateid}" class="delimg" title="{"Mailtemplate entfernen"|__}" onClick="return confirm('{"Mailtemplate wirklich entfernen?"|__}');">&nbsp;</a>
</div>
<div style="width: 2px; height: 30px; background-color: #404040; float:left; margin-left:10px;"></div>
<div style="float:left; margin-left:10px;"><a href="{"mailtemplates_details"|___:$template.templateid}">{$template.label|escape:html}</a><br></div>
<div style="clear:both;"></div>
</li>
{/foreach}
</ul>
{include file="footer.html.tpl"}
