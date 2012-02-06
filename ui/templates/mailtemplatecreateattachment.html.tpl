{include file="header.html.tpl" ansicht="Anhang hochladen"}
<p class="pagetitle">{"Anhang hochladen"|__}</p>
<form action="{"mailtemplateattachment_create"|___:$mailtemplate.templateid}" method="post" enctype="multipart/form-data">
 <fieldset>
  <input type="hidden" name="redirect" value="{if isset($smarty.post.redirect)}{$smarty.post.redirect|stripslashes|escape:html}{else}{$smarty.server.HTTP_REFERER|escape:html}{/if}" />
  <table>
  <tr>
   <th>{"Datei:"|__}</th>
   <td><input type="file" name="attachment" />
  </tr>
  <tr>
   <td colspan="2"><input type="submit" name="save" value="{"Speichern"|__}" /></td>
  </tr>
  </table>
 </fieldset>
</form>
{include file="footer.html.tpl"}

