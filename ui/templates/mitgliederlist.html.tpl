{include file="header.html.tpl" ansicht="Mitgliederverwaltung"}
<p class="pagetitle">Mitgliederverwaltung</p>
<div class="buttonbox">
&nbsp;
<div class="create">
 {foreach from=$mitgliedschaften item=mitgliedschaft}
  <a href="{"mitglieder_create"|___:$mitgliedschaft.mitgliedschaftid}" class="neuset">{"%s"|__:$mitgliedschaft.label}</a>
 {/foreach}
</div>
<div class="search">
 <input type="text" onKeyUp="{literal}if (this.value.length > 3) {$.getJSON('{/literal}{"mitglieder_ajax"|___}{literal}&search=' + this.value, function(data){alert(data.test);})}{/literal}" name="suche" id="suche" />
</div>
<div class="pages">
{if $page > 1}<a href="{"mitglieder_page"|___:'0'}" class="pagebutton">&lt;&lt;</a>{/if}
{if $page > 0}<a href="{"mitglieder_page"|___:$page-1}">&lt;</a>{/if}
{if $page > 3}
{section name=pages loop=$pagecount start=0 max=2}
<a href="{"mitglieder_page"|___:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
{/section}
<span class="pagingsep">...</span>
{section name=pages loop=$pagecount start=$page-1 max=1}
<a href="{"mitglieder_page"|___:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
{/section}
{else}
{section name=pages loop=$pagecount start=0 max=$page}
<a href="{"mitglieder_page"|___:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
{/section}
{/if}

<a href="{"mitglieder_page"|___:$page}"class="curpage">{$page+1}</a>

{if $pagecount-$page > 3}
{section name=pages loop=$pagecount start=$page+1 max=1}
<a href="{"mitglieder_page"|___:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
{/section}
<span class="pagingsep">...</span>
{section name=pages loop=$pagecount start=$pagecount-2 max=2}
<a href="{"mitglieder_page"|___:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
{/section}
{else}
{section name=pages loop=$pagecount start=$page+1 max=$pagecount-$page-1}
<a href="{"mitglieder_page"|___:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
{/section}
{/if}

{if $page < $pagecount-1}<a href="{"mitglieder_page"|___:$page+1}">&gt;</a>{/if}

{if $page < $pagecount-2}<a href="{"mitglieder_page"|___:$pagecount-1}">&gt;&gt;</a>{/if}
</div>
</div>
{include file="mitgliederlist.block.tpl" showmitglieddel=1 mitglieder=$mitglieder}
<div class="buttonbox">
&nbsp;
<div class="pages">
{if $page > 1}<a href="{"mitglieder_page"|___:'0'}" class="pagebutton">&lt;&lt;</a>{/if}

{if $page > 0}<a href="{"mitglieder_page"|___:$page-1}">&lt;</a>{/if}

{if $page > 3}
{section name=pages loop=$pagecount start=0 max=2}
<a href="{"mitglieder_page"|___:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
{/section}
<span class="pagingsep">...</span>
{section name=pages loop=$pagecount start=$page-1 max=1}
<a href="{"mitglieder_page"|___:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
{/section}
{else}
{section name=pages loop=$pagecount start=0 max=$page}
<a href="{"mitglieder_page"|___:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
{/section}
{/if}

<a href="{"mitglieder_page"|___:$page}"class="curpage">{$page+1}</a>

{if $pagecount-$page > 3}
{section name=pages loop=$pagecount start=$page+1 max=1}
<a href="{"mitglieder_page"|___:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
{/section}
<span class="pagingsep">...</span>
{section name=pages loop=$pagecount start=$pagecount-2 max=2}
<a href="{"mitglieder_page"|___:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
{/section}
{else}
{section name=pages loop=$pagecount start=$page+1 max=$pagecount-$page-1}
<a href="{"mitglieder_page"|___:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
{/section}
{/if}

{if $page < $pagecount-1}<a href="{"mitglieder_page"|___:$page+1}">&gt;</a>{/if}

{if $page < $pagecount-2}<a href="{"mitglieder_page"|___:$pagecount-1}">&gt;&gt;</a>{/if}
</div>
<div class="create">
 {foreach from=$mitgliedschaften item=mitgliedschaft}
  <a href="{"mitglieder_create"|___:$mitgliedschaft.mitgliedschaftid}" class="neuset">{"%s"|__:$mitgliedschaft.label}</a>
 {/foreach}
</div>
</div>
{include file="footer.html.tpl"}
