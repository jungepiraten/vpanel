{include file="header.html.tpl" ansicht="Dokumentenverwaltung" menupunkt="dokument"}
<div class="container-fluid">
	<div class="row-fluid">
		{include file="dokumentlist.buttons.tpl" dokumentkategorien=$dokumentkategorien dokumentkategorie=$dokumentkategorie dokumentstatuslist=$dokumentstatuslist dokumentstatus=$dokumentstatus pages=$pages page=$page}
	</div>
{if count($dokumente) > 0}
	<div class="row-fluid">
		<div class="span12">
			{include file="dokumentlist.block.tpl" dokumente=$dokumente}
		</div>
	</div>
{if count($dokumente) > 10}
	<div class="row-fluid">
		{include file="dokumentlist.buttons.tpl" dokumentkategorien=$dokumentkategorien dokumentkategorie=$dokumentkategorie dokumentstatuslist=$dokumentstatuslist dokumentstatus=$dokumentstatus pages=$pages page=$page}
	</div>
{/if}
{/if}
</div>
{include file="footer.html.tpl"}
