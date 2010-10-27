{include file=header.html.tpl}
<form action="{"users_details"|___:$user.userid}" method="post" class="login">
 <fieldset>
  <label for="username">{"Username:"|__}</label>
  <input class="username" type="text" name="username" value="{$user.username|escape:html}" />
  <label for="password">{"Passwort:"|__}</label>
  <input class="password" type="password" name="password" />
  <input class="submit" type="submit" name="save" value="{"Speichern"|__}" />
 </fieldset>
</form>
<div class="roles">
{foreach from=$userroles item=role}
<div class="role">
<a href="{"roles_details"|___:$role.roleid}" class="label">{$role.label}</a> <a href="{"users_delrole"|___:$user.userid:$role.roleid}" class="userdelrole">{"entfernen"|__}</a>
<span class="description">{$role.description}</span>
</div>
{/foreach}
</div>
<form action="{"users_addrole"|___:$user.userid}" method="post" class="useraddrole">
 <fieldset>
  <input type="hidden" name="redirect" value="{$smarty.server.REQUEST_URI}" />
  <select name="roleid">
   {foreach from=$roles item=role}<option value="{$role.roleid|escape:html}">{$role.label|escape:html}</option>{/foreach}
  </select>
  <input class="submit" type="submit" name="do" value="{"Hinzufuegen"|__}" />
 </fieldset>
</form>
<a href="{"users"|___}">{"Benutzeruebersicht"|__}</a>
{include file=footer.html.tpl}
