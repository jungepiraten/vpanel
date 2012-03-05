{include file="header.html.tpl" ansicht="Mailvorlagen verwalten"}
<p class="pagetitle">{"Mailvorlagen verwalten"|__}</p>
<div class="buttonbox">
 <form action="{"mailtemplates"|___}" method="post" class="filter">
  <fieldset>
   <select name="gliederungid" onChange="this.form.submit()">
    <option value="">{"(alle Gliederungen)"|__}</option>
    {foreach from=$gliederungen item=item_gliederung}<option value="{$item_gliederung.gliederungid|escape:html}" {if $gliederung.gliederungid==$item_gliederung.gliederungid}selected="selected"{/if}>{$item_gliederung.label|escape:html}</option>{/foreach}
   </select>
  </fieldset>
 </form>
 <a href="{"mailtemplates_create_gliederungid"|___:$gliederung.gliederungid}" class="neuset">{"Neue Mailvorlage"|__}</a>
</div>
<ul class="entrylist">
{foreach from=$mailtemplates item=template}
<li class="entry{cycle values="odd,even"}">
<div style="float:right; margin-top:7px;margin-right:7px;">
 <a href="{"mailtemplates_del"|___:$template.templateid}" class="delimg" title="{"Mailtemplate entfernen"|__}" onClick="return confirm('{"Mailtemplate wirklich entfernen?"|__}');">&nbsp;</a>
</div>
<div style="float:left; margin-left:10px;"><a href="{"mailtemplates_details"|___:$template.templateid}">{$template.label|escape:html}</a><br>
<span class="description">&nbsp;</span></div>
<div style="clear:both;"></div>
</li>
{/foreach}
</ul>
<div class="buttonbox">
 <form action="{"mailtemplates"|___}" method="post" class="filter">
  <fieldset>
   <select name="gliederungid" onChange="this.form.submit()">
    <option value="">{"(alle Gliederungen)"|__}</option>
    {foreach from=$gliederungen item=item_gliederung}<option value="{$item_gliederung.gliederungid|escape:html}" {if $gliederung.gliederungid==$item_gliederung.gliederungid}selected="selected"{/if}>{$item_gliederung.label|escape:html}</option>{/foreach}
   </select>
  </fieldset>
 </form>
 <a href="{"mailtemplates_create_gliederungid"|___:$gliederung.gliederungid}" class="neuset">{"Neue Mailvorlage"|__}</a>
</div>
{include file="footer.html.tpl"}
