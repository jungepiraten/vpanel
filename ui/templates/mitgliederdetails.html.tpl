{include file=header.html.tpl}
<p class="pagetitle">Mitglied #{$mitglied.mitgliedid} bearbeiten {if isset($mitglied.austritt)}<span class="exmessage">AUSGETRETEN!</span>{/if}</p>
<div class="mainform">
{include file=mitgliederform.block.tpl mitglied=$mitglied}
</div>
<div style="clear:both;">&nbsp;</div>
{include file=footer.html.tpl}
