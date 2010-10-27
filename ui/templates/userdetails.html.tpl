{include file=header.html.tpl}
{include file=userform.block.tpl user=$user}
{include file=rolelist.block.tpl roles=$userroles}
<form action="{"users_addrole"|___:$user.userid}" method="post" class="useraddrole">
 <fieldset>
  <input type="hidden" name="redirect" value="{$smarty.server.REQUEST_URI}" />
  <select name="roleid">
   {foreach from=$roles item=role}<option value="{$role.roleid|escape:html}">{$role.label|escape:html}</option>{/foreach}
  </select>
  <input class="submit" type="submit" name="do" value="{"Hinzufuegen"|__}" />
 </fieldset>
</form>
<a href="{"users"|___}">{"Benutzerverwaltung"|__}</a>
{include file=footer.html.tpl}
