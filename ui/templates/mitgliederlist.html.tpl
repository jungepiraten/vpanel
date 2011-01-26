{include file="header.html.tpl" ansicht="Mitgliederverwaltung"}
<p class="pagetitle">Mitgliederverwaltung</p>
{include file="mitgliederlist.buttons.tpl" mitgliedschaften=$mitgliedschaften filter=$filter pages=$pages page=$page}
{include file="mitgliederlist.block.tpl" showmitglieddel=1 mitglieder=$mitglieder}
{include file="mitgliederlist.buttons.tpl" mitgliedschaften=$mitgliedschaften filter=$filter pages=$pages page=$page}
{include file="footer.html.tpl"}
