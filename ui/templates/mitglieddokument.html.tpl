{include file=header.html.tpl ansicht="Dokument <-> Mitglied"}
<form action="{"mitglieddokument"|___}" method="post" id="mitglieddokumentform">
 <fieldset>
  <input type="hidden" name="mode" value="{$mode}" />
  <input type="hidden" name="redirect" value="{if isset($smarty.post.redirect)}{$smarty.post.redirect|stripslashes|escape:html}{else}{$smarty.server.REQUEST_URI|escape:html}{/if}" />
  <table>
  <tr>
   <th>Mitglied:</th>
   <th>Dokument:</th>
  </tr>
  <tr>
   <td>
    <input type="hidden" id="mitgliedid" name="mitgliedid" value="{if isset($mitglied)}{$mitglied.mitgliedid}{/if}" />
    {if isset($mitglied)}{$mitglied.latest.bezeichnung}{else}
{literal}
<script type="text/javascript">
function selectMitglied(data) {
	document.getElementById("mitgliedid").value = data.mitgliedid;
	document.getElementById("mitglieddokumentform").submit();
}
</script>
{/literal}
    {include file="mitglieder.suche.block.tpl" mitgliedsuchehandler="selectMitglied"}
    {/if}
   </td>
   <td>
    <input type="hidden" id="dokumentid" name="dokumentid" value="{if isset($dokument)}{$dokument.dokumentid}{/if}" />
    {if isset($dokument)}{$dokument.label}{else}
{literal}
<script type="text/javascript">
function selectDokument(data) {
	document.getElementById("dokumentid").value = data.dokumentid;
	document.getElementById("mitglieddokumentform").submit();
}
</script>
{/literal}
    {include file="dokument.suche.block.tpl" dokumentsuchehandler="selectDokument"}
    {/if}
   </td>
  </tr>
  </table>
 </fieldset>
</form>
{include file=footer.html.tpl}
