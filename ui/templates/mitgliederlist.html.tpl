{include file=header.html.tpl ansicht="Mitgliederverwaltung"}
<p class="pagetitle">Mitgliederverwaltung</p>
<div class="create">
 {foreach from=$mitgliedschaften item=mitgliedschaft}
  <a href="{"mitglieder_create"|___:$mitgliedschaft.mitgliedschaftid}" class="neuset">{"%s"|__:$mitgliedschaft.label}</a>
 {/foreach}
</div>
<div class="pages">
 {section name=pages loop=$pagecount start=0}
  <a href="{"mitglieder_page"|___:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
 {/section}
</div>
{include file=mitgliederlist.block.tpl showmitglieddel=1 mitglieder=$mitglieder}
<div class="pages">
 {section name=pages loop=$pagecount start=0}
  <a href="{"mitglieder_page"|___:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a>
 {/section}
</div>
<div class="create">
 {foreach from=$mitgliedschaften item=mitgliedschaft}
  <a href="{"mitglieder_create"|___:$mitgliedschaft.mitgliedschaftid}" class="neuset">{"%s"|__:$mitgliedschaft.label}</a>
 {/foreach}
</div>
{include file=footer.html.tpl}
