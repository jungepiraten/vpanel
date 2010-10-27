<form action="{if isset($role)}{"role_details"|___:$role.roleid}{else}{"role_create"|___}{/if}" method="post" class="login">
 <fieldset>
  <label for="label">{"Bezeichnung:"|__}</label>
  <input class="label" type="text" name="label" value="{if isset($role)}{$role.label|escape:html}{/if}" />
  <label for="description">{"Beschreibung:"|__}</label>
  <textarea name="description">{if isset($role)}{$role.description|escape:html}{/if}</textarea>
  <input class="submit" type="submit" name="save" value="{"Speichern"|__}" />
 </fieldset>
</form>
