{include file="header.html.tpl" ansicht="Beitragsverwaltung"}
<p class="pagetitle">Beitragsverwaltung</p>
{include file="beitraglist.buttons.tpl"}
{if count($beitraege) > 0}
{include file="beitraglist.block.tpl" beitraege=$beitraege}
{include file="beitraglist.buttons.tpl"}
{/if}
{include file="footer.html.tpl"}
