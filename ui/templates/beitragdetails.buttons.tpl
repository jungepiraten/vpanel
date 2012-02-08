<div class="buttonbox">
&nbsp;
<div class="pages">
{if $page > 1} <a href="{"beitraege_details_page"|___:$beitrag.beitragid:'0'}" class="pagebutton">&lt;&lt;</a>{/if}
{if $page > 0} <a href="{"beitraege_details_page"|___:$beitrag.beitragid:$page-1}">&lt;</a>{/if}
{if $page > 3}
{section name=pages loop=$pagecount start=0 max=2}
 <a href="{"beitraege_details_page"|___:$beitrag.beitragid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
{/section}
 <span class="pagingsep">...</span>
{section name=pages loop=$pagecount start=$page-1 max=1}
 <a href="{"beitraege_details_page"|___:$beitrag.beitragid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
{/section}
{else}
{section name=pages loop=$pagecount start=0 max=$page}
 <a href="{"beitraege_details_page"|___:$beitrag.beitragid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
{/section}
{/if}
 <a href="{"beitraege_details_page"|___:$beitrag.beitragid:$page}"class="curpage">{$page+1}</a>
{if $pagecount-$page > 3}
{section name=pages loop=$pagecount start=$page+1 max=1}
 <a href="{"beitraege_details_page"|___:$beitrag.beitragid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
{/section}
 <span class="pagingsep">...</span>
{section name=pages loop=$pagecount start=$pagecount-2 max=2}
 <a href="{"beitraege_details_page"|___:$beitrag.beitragid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
{/section}
{else}
{section name=pages loop=$pagecount start=$page+1 max=$pagecount-$page-1}
 <a href="{"beitraege_details_page"|___:$beitrag.beitragid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
{/section}
{/if}
{if $page < $pagecount-1} <a href="{"beitraege_details_page"|___:$beitrag.beitragid:$page+1}">&gt;</a>{/if}
{if $page < $pagecount-2} <a href="{"beitraege_details_page"|___:$beitrag.beitragid:$pagecount-1}">&gt;&gt;</a>{/if}
</div>
</div>
