{include file="header.html.tpl" ansicht="Mitglied bearbeiten"}
<p class="pagetitle">Mitglied #{$mitglied.mitgliedid} ({if isset($mitglied.latest.natperson)}{$mitglied.latest.natperson.vorname|escape:html} {$mitglied.latest.natperson.name|escape:html}{/if}
{if isset($mitglied.latest.jurperson)}{$mitglied.latest.jurperson.label|escape:html}{/if}) bearbeiten.</p>
{if isset($mitglied.austritt)}<p class="exmessage">AUSGETRETEN!</span>{/if}
<div>
{include file="mitgliederform.block.tpl" mitglied=$mitglied}
</div>
<div style="clear:both;">&nbsp;</div>
{include file="footer.html.tpl"}
