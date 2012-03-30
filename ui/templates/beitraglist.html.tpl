{include file="header.html.tpl" ansicht="Beitragsverwaltung" menupunkt="beitrag"}
{include file="beitraglist.buttons.tpl"}
{include file="beitraglist.block.tpl" beitraege=$beitraege}
{if count($beitraege) > 10}
	{include file="beitraglist.buttons.tpl"}
{/if}
{include file="footer.html.tpl"}
