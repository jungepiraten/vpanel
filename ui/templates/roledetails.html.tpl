{include file="header.html.tpl" ansicht="Rolle bearbeiten"}
<div>
<p class="pagetitle">Rolle &quot;{if isset($role)}{$role.label|escape:html}{/if}&quot; bearbeiten</p>
<div class="mainform">
{include file="roleform.block.tpl" role=$role}
<hr>
<span style="font-weight:bold; font-size:14px;">Mitglieder:</span>
{include file="userlist.block.tpl" roleid=$role.roleid showroledel=1 users=$roleusers}
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
  <select name="gliederungid" onChange="selectPermissionList(this)">
   <option value="">{"Global"|__}</option>
   {foreach from=$gliederungen item=gliederung}<option value="{$gliederung.gliederungid|escape:html}">{$gliederung.label|escape:html}</option>{/foreach}
  </select>
  <table class="permissionlist" name="permissionlist" id="permissionlist_">
  {foreach from=$permissions_global item=permission}
  <tr class="permissionrow">
   <td><input type="checkbox" name="permissions[]" value="{$permission.permissionid|escape:html}" {foreach from=$rolepermissions item=rolepermission}{if $permission.permissionid == $rolepermission.permission.permissionid}checked="checked"{/if}{/foreach} /></td>
   <th>{$permission.label|__|escape:html}</th>
  </tr>
  {/foreach}  
  </table>
  {foreach from=$gliederungen item=gliederung}
  <table class="permissionlist" name="permissionlist" id="permissionlist_{$gliederung.gliederungid}">
  {foreach from=$permissions_local item=permission}
  <tr class="permissionrow">
   <td><input type="checkbox" name="permissions[]" value="{$permission.permissionid|escape:html}-{$gliederung.gliederungid}" {foreach from=$rolepermissions item=rolepermission}{if $gliederung.gliederungid == $rolepermission.gliederung.gliederungid and $permission.permissionid == $rolepermission.permission.permissionid}checked="checked"{/if}{/foreach} /></td>
   <th>{$permission.label|__|escape:html}</th>
  </tr>
  {/foreach}
  </table>
  {/foreach}
  <input type="submit" name="savepermissions" value="{"Speichern"|__}" />
 </fieldset>
</form>
{literal}
<script type="text/javascript">

function selectPermissionList(elem) {
	var permissionlist = "permissionlist_" + elem.options[elem.selectedIndex].value;
	var elements = document.getElementsByName("permissionlist");
	for (var element in elements) {
		elements[element].style.display = (elements[element].id == permissionlist) ? "block" : "none";
	}
}

selectPermissionList(document.getElementsByName("gliederungid")[0]);

</script>
{/literal}
</div>
<div style="clear:both;">&nbsp;</div>
</div>
{include file="footer.html.tpl"}
