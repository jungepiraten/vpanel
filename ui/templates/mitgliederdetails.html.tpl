{include file="header.html.tpl" ansicht="Mitglied bearbeiten"}
<p class="pagetitle">Mitglied #{$mitglied.mitgliedid} ({if isset($mitglied.latest.natperson)}{$mitglied.latest.natperson.vorname|escape:html} {$mitglied.latest.natperson.name|escape:html}{/if}
{if isset($mitglied.latest.jurperson)}{$mitglied.latest.jurperson.label|escape:html}{/if}) bearbeiten.</p>
{if isset($mitglied.austritt)}<p class="exmessage">AUSGETRETEN!</span>{/if}
<form action="{"mitglieder_details"|___:$mitglied.mitgliedid}" method="post">
 <fieldset>
  <select name="revisionid" onChange="this.form.submit()">
   {foreach from=$mitgliedrevisions item=rev}
   <option value="{$rev.revisionid}" {if $rev.revisionid == $mitgliedrevision.revisionid}selected="selected"{/if}>Version vom {$rev.timestamp|date_format:"%d.%m.%Y"} um {$rev.timestamp|date_format:"%H:%M"} Uhr von {$rev.user.username}</option>
   {/foreach}
  </select>
 </fieldset>
</form>
<div>
{include file="mitgliederform.block.tpl" mitglied=$mitglied}
</div>
<div style="clear:both;">&nbsp;</div>
{include file="footer.html.tpl"}
