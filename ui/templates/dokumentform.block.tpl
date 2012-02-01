<form action="{if isset($dokument)}{"dokumente_details"|___:$dokument.dokumentid}{else}{"dokumente_create"|___}{/if}" method="post" class="filter" enctype="multipart/form-data">
 <fieldset>
  <table>
  <tr>
   <th>{"Kategorie:"|__}</th>
   <td>{if isset($dokument)}
    {include file=dokumentkategoriedropdown.block.tpl defaulttext="(auswählen)" selecteddokumentkategorieid=$dokument.dokumentkategorie.dokumentkategorieid}
   {else}
    {include file=dokumentkategoriedropdown.block.tpl defaulttext="(auswählen)" selecteddokumentkategorieid=$dokumentkategorie.dokumentkategorieid}
   {/if}</td>
  </tr>
  <tr>
   <th>{"Status:"|__}</th>
   <td>{if isset($dokument)}
    {include file=dokumentstatusdropdown.block.tpl defaulttext="(auswählen)" selecteddokumentstatusid=$dokument.dokumentstatus.dokumentstatusid}
   {else}
    {include file=dokumentstatusdropdown.block.tpl defaulttext="(auswählen)" selecteddokumentstatusid=$dokumentstatus.dokumentstatusid}
   {/if}</td>
  </tr>
  <tr>
   <th>{"Datei:"|__}</th>
   <td>{if isset($dokument)}<a href="{"dokumente_get"|___:$dokument.dokumentid}">{"Download"|__}</a>{else}<input type="file" name="file" />{/if}</td>
  </tr>
  <tr>
   <th>{"Identifikation:"|__}</th>
   <td>{if isset($dokument)}{$dokument.identifier|escape:html}{else}<input type="text" name="idkey" size="40" />{/if}</td>
  </tr>
  <tr>
   <th>{"Titel:"|__}</th>
   <td><input type="text" name="label" size="40" value="{if isset($dokument)}{$dokument.label}{/if}" /></td>
  </tr>
  <tr>
   <th>{"Kommentar:"|__}</th>
   <td><textarea name="kommentar" cols="40" rows="10"></textarea></td>
  </tr>
  <tr>
   <th colspan="2"><input type="submit" class="submit" name="save" value="{"Speichern"|__}" /></th>
  </tr>
  </table>
 </fieldset>
</form>
