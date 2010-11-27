{include file=header.html.tpl ansicht="Mitgliederverwaltung"}
<p class="pagetitle">Mitgliederverwaltung</p>
<div class="buttonbox">
&nbsp;
<div class="create">
 {foreach from=$mitgliedschaften item=mitgliedschaft}
  <a href="{"mitglieder_create"|___:$mitgliedschaft.mitgliedschaftid}" class="neuset">{"%s"|__:$mitgliedschaft.label}</a>
 {/foreach}
</div>
<div class="pages">
{if $smarty.get.page > 1}<a href="{"mitglieder_page"|___:'0'}" class="pagebutton">&lt;&lt;</a>{/if}

{if $smarty.get.page > 0}<a href="{"mitglieder_page"|___:$smarty.get.page-1}">&lt;</a>{/if}
 {section name=pages loop=$pagecount start=0}
<a href="{"mitglieder_page"|___:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
 {/section}
{if $smarty.get.page < $pagecount-1}<a href="{"mitglieder_page"|___:$smarty.get.page+1}">&gt;</a>{/if}

{if $smarty.get.page < $pagecount-2}<a href="{"mitglieder_page"|___:$pagecount-1}">&gt;&gt;</a>{/if}
</div>
</div>
{include file=mitgliederlist.block.tpl showmitglieddel=1 mitglieder=$mitglieder}
<div class="buttonbox">
&nbsp;
<div class="pages">
{if $smarty.get.page > 1}<a href="{"mitglieder_page"|___:'0'}">&lt;&lt;</a>{/if}
{if $smarty.get.page > 0}<a href="{"mitglieder_page"|___:$smarty.get.page-1}">&lt;</a>{/if}
 {section name=pages loop=$pagecount start=0}
<a href="{"mitglieder_page"|___:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
 {/section}
{if $smarty.get.page < $pagecount-1}<a href="{"mitglieder_page"|___:$smarty.get.page+1}">&gt;</a>{/if}
{if $smarty.get.page < $pagecount-2}<a href="{"mitglieder_page"|___:$pagecount-1}">&gt;&gt;</a>{/if}
</div>
<div class="create">
 {foreach from=$mitgliedschaften item=mitgliedschaft}
  <a href="{"mitglieder_create"|___:$mitgliedschaft.mitgliedschaftid}" class="neuset">{"%s"|__:$mitgliedschaft.label}</a>
 {/foreach}
</div>
</div>
{include file=footer.html.tpl}
