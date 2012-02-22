{include file="header.html.tpl" ansicht="Mitglied bearbeiten"}
<p class="pagetitle">Mitglied #{$mitglied.mitgliedid} ({if isset($mitglied.latest.natperson)}{$mitglied.latest.natperson.vorname|escape:html} {$mitglied.latest.natperson.name|escape:html}{/if}
{if isset($mitglied.latest.jurperson)}{$mitglied.latest.jurperson.label|escape:html}{/if}) bearbeiten.</p>
<div class="buttonbox">
 <form action="{"mitglieder_details"|___:$mitglied.mitgliedid}" method="post" class="revision">
  <fieldset>
   <select name="revisionid" onChange="this.form.submit()">
    {foreach from=$mitgliedrevisions item=rev}
    <option value="{$rev.revisionid}" {if $rev.revisionid == $mitgliedrevision.revisionid}selected="selected"{/if}>Version vom {$rev.timestamp|date_format:"%d.%m.%Y"} um {$rev.timestamp|date_format:"%H:%M"} Uhr{if isset($rev.user)} von {$rev.user.username}{/if}</option>
    {/foreach}
   </select>
  </fieldset>
 </form>
 {include file="mitgliederfilter.options.tpl" filterid=$mitglied.filterid}
</div>
<table>
<tr>
 <th>Eingetreten</th>
 <td>{$mitglied.eintritt|date_format:"%d.%m.%Y"}</td>
</tr>
{if isset($mitglied.austritt)}
<tr>
 <th>Ausgetreten</th>
 <td>{$mitglied.austritt|date_format:"%d.%m.%Y"}</td>
</tr>
{/if}
</table>
{include file="mitgliederform.block.tpl" mitglied=$mitglied}

<div style="position:relative;">
<div style="float:left; width:400px;">
{foreach from=$mitgliednotizen item=notiz}
<div class="notiz">
 <span class="meta">{if isset($notiz.author)}{"Von %s"|__:$notiz.author.username}{/if}</span>
 <div class="kommentar">{$notiz.kommentar}</div>
</div>
{/foreach}
<form action="{"mitglieder_details"|___:$mitglied.mitgliedid}" method="post">
 <fieldset>
  <table>
  <tr>
   <td colspan="2"><textarea cols="35" rows="5" name="kommentar"></textarea></td>
  </tr>
  <tr>
   <th colspan="2"><input type="submit" name="addnotiz" value="{"Notiz speichern"|__}" /></th>
  </tr>
  </table>
 </fieldset>
</form>
</div>

<div style="float:left; width:400px; margin-left:50px;">
<script type="text/javascript">

var beitragHoehe = new Array();
{foreach from=$beitraege item=beitrag}
beitragHoehe[{$beitrag.beitragid}] = "{if $beitrag.hoehe != null}{$beitrag.hoehe}{else}{$mitglied.latest.beitrag}{/if}";
{/foreach}

{literal}
function changeBeitragNeuHoehe(beitragID) {
	document.getElementById("beitrag_neu_hoehe").value = beitragHoehe[beitragID];
}
{/literal}

</script>
<form action="{"mitglieder_beitraege"|___:$mitglied.mitgliedid}" method="post">
 <fieldset>
<table>
<tr>
 <th>&nbsp;</th>
 <th>Beitrag</th>
 <th>Bezahlt</th>
 <th>Ausstehend</th>
</tr>
{foreach from=$mitglied.beitraege item=beitrag}
<tr>
 <th><a href="{"beitraege_details"|___:$beitrag.beitrag.beitragid}">{$beitrag.beitrag.label|escape:html}</a></th>
 <td><input type="text" size="5" name="beitraege_hoehe[{$beitrag.beitrag.beitragid}]" value="{$beitrag.hoehe|string_format:"%.2f"}" /></td>
 <td><input type="text" size="5" name="beitraege_bezahlt[{$beitrag.beitrag.beitragid}]" value="{$beitrag.bezahlt|string_format:"%.2f"}" /></td>
 <td>{$beitrag.hoehe-$beitrag.bezahlt|string_format:"%.2f"}</td>
 <td><a href="{"mitglieder_beitraege_del"|___:$mitglied.mitgliedid:$beitrag.beitrag.beitragid}" class="delimg">&nbsp;</a></td>
</tr>
{/foreach}
{if count($beitraege) > count($mitglied.beitraege)}
<tr>
 <th><select name="beitrag_neu_beitragid" onchange="changeBeitragNeuHoehe(this.value);">
   <option value="">{"(nichts hinzufügen)"|__}</option>
{foreach from=$beitraege item=beitrag}{assign var=beitragid value=$beitrag.beitragid}{if !isset($mitglied.beitraege.$beitragid)}
   <option value="{$beitrag.beitragid|escape:html}">{$beitrag.label|escape:html}</option>
{/if}{/foreach}
  </select></th>
 <td><input type="text" size="5" name="beitrag_neu_hoehe" id="beitrag_neu_hoehe" /></td>
 <td><input type="text" size="5" name="beitrag_neu_bezahlt" /></td>
 <td>&nbsp;</td>
 <td>&nbsp;</td>
</tr>
{/if}
<tr>
 <th>Summe</th>
 <th>{$mitglied.beitraege_hoehe|string_format:"%.2f"}</th>
 <th>{$mitglied.beitraege_bezahlt|string_format:"%.2f"}</th>
 <th>{$mitglied.beitraege_hoehe-$mitglied.beitraege_bezahlt|string_format:"%.2f"}</th>
 <th>&nbsp;</th>
</tr>
<tr>
 <td colspan="5"><input type="submit" name="save" value="Speichern" /></td>
</tr>
</table>
 </fieldset>
</form>
</div>

<div style="float:left; width:400px; margin-left:50px;">
<div class="buttonbox">
 <a href="{"mitglieder_dokument"|___:$mitglied.mitgliedid}">Dokument verlinken</a>
</div>
{if count($dokumente) > 0}
{include file=dokumentlist.block.tpl dokumente=$dokumente showmitglieddokumentdel=1}
<div class="buttonbox">
 <a href="{"mitglieder_dokument"|___:$mitglied.mitgliedid}">Dokument verlinken</a>
</div>
{/if}
</div>
</div>

<div style="clear:left;">&nbsp;</div>
</div>
{include file="footer.html.tpl"}
