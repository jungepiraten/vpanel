{include file=header.html.tpl ansicht="Rolle bearbeiten"}
<div>
<p class="pagetitle">Rolle &quot;{if isset($role)}{$role.label|escape:html}{/if}&quot; bearbeiten</p>
<div class="mainform">
{include file=roleform.block.tpl role=$role}
<hr>
<span style="font-weight:bold; font-size:14px;">Mitglieder:</span>
{include file=userlist.block.tpl roleid=$role.roleid showroledel=1 users=$roleusers}
<form action="{"roles_adduser"|___:$role.roleid}" method="post" class="roleadduser">
 <fieldset>
  <input type="hidden" name="redirect" value="{$smarty.server.REQUEST_URI}" />
  <select name="userid">
   {foreach from=$users item=user}<option value="{$user.userid|escape:html}">{$user.username|escape:html}</option>{/foreach}
  </select>
  <input class="submit" type="submit" name="do" value="{"Hinzufuegen"|__}" />
 </fieldset>
</form>
</div>
<div class="sideinfo">
<span class="sideinfoheader">Benutzerrechte:</span>
<form action="{"roles_details"|___:$role.roleid}" method="post" class="permissions">
 <fieldset>
  <table class="permissionlist">
  {foreach from=$permissions item=permission}
  <tr class="permissionrow">
   <td><input type="checkbox" name="permissions[]" value="{$permission.permissionid|escape:html}" {foreach from=$rolepermissions item=rolepermission} {foreach from=$rolepermission item=roleperm}{if $permission.permissionid == $roleperm}checked="checked"{/if}{/foreach}{/foreach} /></td>
   <th>{$permission.label|__|escape:html}</th>
   <td>{$permission.description|escape:html}</td>
  </tr>
  {/foreach}
  </table>
  <input type="submit" name="savepermissions" value="{"Speichern"|__}" />
 </fieldset>
</form>
</div>
<div style="clear:both;">&nbsp;</div>
</div>
{include file=footer.html.tpl}
