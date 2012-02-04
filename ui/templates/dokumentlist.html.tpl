{include file="header.html.tpl" ansicht="Dokumentenverwaltung" sidebar="dokument.suche.block.tpl"}
<p class="pagetitle">Dokumentenverwaltung</p>
{include file="dokumentlist.buttons.tpl" dokumentkategorien=$dokumentkategorien dokumentkategorie=$dokumentkategorie dokumentstatuslist=$dokumentstatuslist dokumentstatus=$dokumentstatus pages=$pages page=$page}
{include file="dokumentlist.block.tpl" dokumente=$dokumente}
{include file="dokumentlist.buttons.tpl" dokumentkategorien=$dokumentkategorien dokumentkategorie=$dokumentkategorie dokumentstatuslist=$dokumentstatuslist dokumentstatus=$dokumentstatus pages=$pages page=$page}
{include file="footer.html.tpl"}
