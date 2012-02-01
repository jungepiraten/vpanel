<div class="buttonbox">
&nbsp;
<div class="create">
 <form action="{"dokumente"|___}" method="post" class="filter">
  <fieldset>
   {if isset($dokumentstatus)}<input type="hidden" name="statusid" value="{$dokumentstatus.dokumentstatusid}" />{/if}
   <select name="kategorieid" onChange="this.form.submit()">
    <option value="">{"(alle Kategorien)"|__}</option>
    {foreach from=$dokumentkategorien item=item_kategorie}<option value="{$item_kategorie.dokumentkategorieid|escape:html}" {if $dokumentkategorie.dokumentkategorieid==$item_kategorie.dokumentkategorieid}selected="selected"{/if}>{$item_kategorie.label|escape:html}</option>{/foreach}
   </select>
  </fieldset>
 </form>
 <form action="{"dokumente"|___}" method="post" class="filter">
  <fieldset>
   {if isset($dokumentkategorie)}<input type="hidden" name="kategorieid" value="{$dokumentkategorie.dokumentkategorieid}" />{/if}
   <select name="statusid" onChange="this.form.submit()">
    <option value="">{"(alle Zustände)"|__}</option>
    {foreach from=$dokumentstatuslist item=item_status}<option value="{$item_status.dokumentstatusid|escape:html}" {if $dokumentstatus.dokumentstatusid==$item_status.dokumentstatusid}selected="selected"{/if}>{$item_status.label|escape:html}</option>{/foreach}
   </select>
  </fieldset>
 </form>
 <a href="{"dokumente_create"|___:$dokumentkategorie.dokumentkategorieid:$dokumentstatus.dokumentstatusid}">{"Neu"|__}</a>
</div>
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
