{include file="header.html.tpl" ansicht="Mitgliederverwaltung" menupunkt="mitglied"}
<p>{$mitgliedercount|escape:html} Eintr&auml;ge gefunden</p>
<div class="container-fluid">
	<div class="row-fluid">
		{include file="mitgliederlist.buttons.tpl" mitgliedschaften=$mitgliedschaften filter=$filter pages=$pages page=$page}
	</div>
{if count($mitglieder) > 0}
	<div class="row-fluid">
		<div class="span12">
			{include file="mitgliederlist.block.tpl" showmitglieddel=1 mitglieder=$mitglieder}
		</div>
	</div>
{if count($mitglieder) > 10}
	<div class="row-fluid">
		{include file="mitgliederlist.buttons.tpl" mitgliedschaften=$mitgliedschaften filter=$filter pages=$pages page=$page}
	</div>
{/if}
{/if}
</div>
{include file="footer.html.tpl"}
