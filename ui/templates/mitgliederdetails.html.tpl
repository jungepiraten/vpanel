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
{include file=dokumentlist.block.tpl dokumente=$dokumente}
<a href="{"mitglieder_dokument"|___:$mitglied.mitgliedid}">Dokument verlinken</a>
{foreach from=$mitgliednotizen item=notiz}
<div class="notiz">
 <span class="meta">{"Von %s"|__:$notiz.author.username}</span>
 <div class="kommentar">{$notiz.kommentar}</div>
</div>
{/foreach}
<form action="{"mitglieder_details"|___:$mitglied.mitgliedid}" method="post">
 <fieldset>
  <table>
  <tr>
   <td colspan="2"><textarea rows="6" cols="25" name="kommentar"></textarea></td>
  </tr>
  <tr>
   <th colspan="2"><input type="submit" name="addnotiz" value="{"Notiz speichern"|__}" /></th>
  </tr>
  </table>
 </fieldset>
</form>
<div style="clear:both;">&nbsp;</div>
{include file="footer.html.tpl"}
