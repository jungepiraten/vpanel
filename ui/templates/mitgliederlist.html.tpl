{include file=header.html.tpl}
<p class="pagetitle">Mitgliederverwaltung</p>
{foreach from=$mitgliedschaften item=mitgliedschaft}
 <a href="{"mitglieder_create"|___:$mitgliedschaft.mitgliedschaftid}" class="neuset">{"%s"|__:$mitgliedschaft.label}</a>
{/foreach}
{include file=mitgliederlist.block.tpl showmitglieddel=1 mitglieder=$mitglieder}
{foreach from=$mitgliedschaften item=mitgliedschaft}
 <a href="{"mitglieder_create"|___:$mitgliedschaft.mitgliedschaftid}" class="neuset">{"%s"|__:$mitgliedschaft.label}</a>
{/foreach}
{include file=footer.html.tpl}
