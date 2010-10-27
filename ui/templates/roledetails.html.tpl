{include file=header.html.tpl}
<form action="{"role_details"|___:$role.roleid}" method="post" class="login">
 <fieldset>
  <label for="label">{"Bezeichnung:"|__}</label>
  <input class="label" type="text" name="label" value="{$role.label|escape:html}" />
  <label for="description">{"Beschreibung:"|__}</label>
  <textarea name="description">{$role.description|escape:html}</textarea>
  <input class="submit" type="submit" name="save" value="{"Speichern"|__}" />
 </fieldset>
</form>
<ul class="users">
{foreach from=$roleusers item=user}
 <li class="user"><a href="{"users_details"|___:$user.userid}">{$user.username}</a> <a href="{"roles_deluser"|___:$user.userid:$role.roleid}" class="roledeluser">{"entfernen"|__}</a></li>
{/foreach}
</ul>
<form action="{"roles_adduser"|___:$role.roleid}" method="post" class="roleadduser">
 <fieldset>
  <input type="hidden" name="redirect" value="{$smarty.server.REQUEST_URI}" />
  <select name="userid">
   {foreach from=$users item=user}<option value="{$user.userid|escape:html}">{$user.username|escape:html}</option>{/foreach}
  </select>
  <input class="submit" type="submit" name="do" value="{"Hinzufuegen"|__}" />
 </fieldset>
</form>
<a href="{"roles"|___}">{"Rollenuebersicht"|__}</a>
{include file=footer.html.tpl}
