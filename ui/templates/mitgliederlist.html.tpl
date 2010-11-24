{include file=header.html.tpl}
<p class="pagetitle">Mitgliederverwaltung</p>
{foreach from=$mitgliedschaften item=mitgliedschaft}
 <a href="{"mitglieder_create"|___:$mitgliedschaft.mitgliedschaftid}" class="neuset">{"Neu: %s"|__:$mitgliedschaft.label}</a>
{/foreach}
{include file=rolelist.block.tpl showroledel=1 roles=$roles}
{foreach from=$mitgliedschaften item=mitgliedschaft}
 <a href="{"mitglieder_create"|___:$mitgliedschaft.mitgliedschaftid}" class="neuset">{"Neu: %s"|__:$mitgliedschaft.label}</a>
{/foreach}
{include file=footer.html.tpl}
