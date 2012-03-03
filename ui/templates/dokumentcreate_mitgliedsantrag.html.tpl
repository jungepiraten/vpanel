{include file="header.html.tpl" ansicht="Neuen Mitgliedsantrag anlegen"}
<p class="pagetitle">Neuen Mitgliedsantrag anlegen</p>
<form action="{"dokumente_create"|___}" method="post" enctype="multipart/form-data">
 <fieldset>
  <input type="hidden" name="dokumenttemplateid" value="{$dokumenttemplate.dokumenttemplateid|escape:html}" />
  <table>
  <tr>
   <th>{"Datei:"|__}</th>
   <td><input type="file" name="file" /></td>
  </tr>
  <tr>
   <th>{"Name:"|__}</th>
   <td><input type="text" name="vorname" size="20" /> <input type="text" name="name" size="20" /></td>
  </tr>
  <tr>
   <th>{"Geburtsdatum:"|__}</th>
   <td><input type="text" name="geburtsdatum" size="20" /></td>
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
