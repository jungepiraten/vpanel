<div class="buttonbox">
&nbsp;
 <form action="{"mitglieder"|___}" method="post" class="filter">
  <fieldset>
   <select name="filterid" onChange="this.form.submit()">
    <option value="">{"(kein Filter)"|__}</option>
    {foreach from=$filters item=item_filter}<option value="{$item_filter.filterid|escape:html}" {if $filter.filterid==$item_filter.filterid}selected="selected"{/if}>{$item_filter.label|escape:html}</option>{/foreach}
   </select>
  </fieldset>
 </form>
 {include file="mitgliederfilter.options.tpl" filterid=$filter.filterid}
 <form action="{"mitglieder_create"|___}" method="post" class="filter">
  <fieldset>
   <select name="mitgliedtemplateid">
    <option value="">{"(keine Vorlage)"|__}</option>
    {foreach from=$mitgliedtemplates item=mitgliedtemplate}<option value="{$mitgliedtemplate.mitgliedtemplateid|escape:html}">{$mitgliedtemplate.label|escape:html}</option>{/foreach}
   </select>
   <input type="submit" value="{"Anlegen"|__}" />
  </fieldset>
 </form>
<div class="pages">
{if $page > 1} <a href="{"mitglieder_page"|___:$filter.filterid:'0'}" class="pagebutton">&lt;&lt;</a>{/if}
{if $page > 0} <a href="{"mitglieder_page"|___:$filter.filterid:$page-1}">&lt;</a>{/if}
{if $page > 3}
{section name=pages loop=$pagecount start=0 max=2}
 <a href="{"mitglieder_page"|___:$filter.filterid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
{/section}
 <span class="pagingsep">...</span>
{section name=pages loop=$pagecount start=$page-1 max=1}
 <a href="{"mitglieder_page"|___:$filter.filterid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
{/section}
{else}
{section name=pages loop=$pagecount start=0 max=$page}
 <a href="{"mitglieder_page"|___:$filter.filterid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
{/section}
{/if}
 <a href="{"mitglieder_page"|___:$filter.filterid:$page}"class="curpage">{$page+1}</a>
{if $pagecount-$page > 3}
{section name=pages loop=$pagecount start=$page+1 max=1}
 <a href="{"mitglieder_page"|___:$filter.filterid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
{/section}
 <span class="pagingsep">...</span>
{section name=pages loop=$pagecount start=$pagecount-2 max=2}
 <a href="{"mitglieder_page"|___:$filter.filterid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
{/section}
{else}
{section name=pages loop=$pagecount start=$page+1 max=$pagecount-$page-1}
 <a href="{"mitglieder_page"|___:$filter.filterid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
{/section}
{/if}
{if $page < $pagecount-1} <a href="{"mitglieder_page"|___:$filter.filterid:$page+1}">&gt;</a>{/if}
{if $page < $pagecount-2} <a href="{"mitglieder_page"|___:$filter.filterid:$pagecount-1}">&gt;&gt;</a>{/if}
</div>
</div>
