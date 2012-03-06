{include file="header.html.tpl" ansicht="Neues Dokument anlegen"}
<p class="pagetitle">{$dokumenttemplate.label} anlegen</p>
<form action="{"dokumente_create"|___}" method="post" class="filter" enctype="multipart/form-data">
 <fieldset>
  <input type="hidden" name="dokumenttemplateid" value="{$dokumenttemplate.dokumenttemplateid|escape:html}" />
  <table>
  <tr>
   <th>{"Eingang:"|__}</th>
   <td><input type="text" name="timestamp" value="{$smarty.now|date_format:"%d.%m.%Y"}" /></td>
  </tr>
  <tr>
   <th>{"Datei:"|__}</th>
   <td><input type="file" name="file" /></td>
  </tr>
  <tr>
   <th>{"Titel:"|__}</th>
   <td><input type="text" name="label" size="40" /></td>
  </tr>
  <tr>
   <th>{"Kommentar:"|__}</th>
   <td><textarea name="kommentar" cols="40" rows="10"></textarea></td>
  </tr>
  <tr>
   <td colspan="2"><input type="submit" class="submit" name="save" value="{"Speichern"|__}" /></td>
  </tr>
  </table>
 </fieldset>
</form>

{include file="footer.html.tpl"}
