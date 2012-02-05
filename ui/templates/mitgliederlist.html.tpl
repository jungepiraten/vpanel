{include file="header.html.tpl" ansicht="Mitgliederverwaltung" sidebar="mitglieder.suche.block.tpl"}
<p class="pagetitle">Mitgliederverwaltung</p>
{include file="mitgliederlist.buttons.tpl" mitgliedschaften=$mitgliedschaften filter=$filter pages=$pages page=$page}
{if count($mitglieder) > 0}
{include file="mitgliederlist.block.tpl" showmitglieddel=1 mitglieder=$mitglieder}
{include file="mitgliederlist.buttons.tpl" mitgliedschaften=$mitgliedschaften filter=$filter pages=$pages page=$page}
{/if}
{include file="footer.html.tpl"}
