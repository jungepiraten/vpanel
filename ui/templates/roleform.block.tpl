<form action="{if isset($role)}{"role_details"|___:$role.roleid}{else}{"role_create"|___}{/if}" method="post">
 <fieldset>
 <table>
     <tr>
         <td><label for="label">{"Bezeichnung:"|__}</label></td>
         <td><input class="label" type="text" name="label" value="{if isset($role)}{$role.label|escape:html}{/if}" /></td>
     </tr>
     <tr>
         <td><label for="description">{"Beschreibung:"|__}</label></td>
         <td><textarea name="description" cols="40" rows="5">{if isset($role)}{$role.description|escape:html}{/if}</textarea></td>
     </tr>
     <tr>
         <td><input class="submit" type="submit" name="save" value="{"Speichern"|__}" /></td>
     </tr>
 </table>
 </fieldset>
</form>
