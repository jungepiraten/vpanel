{capture assign=ansicht}Rolle <em>&raquo;{$role.label|escape:html}&laquo;</em> bearbeiten{/capture}
{include file="header.html.tpl" ansicht="Rolle bearbeiten" menupunkt="role"}
<div class="container-fluid">
<div class="row-fluid">

	<div class="span6">
		{include file="roleform.block.tpl" role=$role}
 
		<h2>Mitglieder</h2>
		{include file="userlist.block.tpl" roleid=$role.roleid showroledel=1 users=$roleusers}
		<div class="btn-group">
			<a class="btn btn-success dropdown-toggle" data-toggle="dropdown" href="#">
				{"Benutzer*in hinzufügen"|__}
				<span class="caret"></span>
				</a>
			<ul class="dropdown-menu">
			{foreach from=$users item=user}
				<li><a href="{"users_addrole"|___:$user.userid:$role.roleid}">{$user.username|escape:html}</a></li>
			{/foreach}
			</ul>
		</div>
	</div>

	<div class="span6">
		<h2>Benutzer*innenrechte</h2>
		<form action="{"roles_details"|___:$role.roleid}" method="post" class="form-inline">
			<fieldset>
				<select name="gliederungid" onChange="selectPermissionList(this)">
					<option value="">{"Global"|__}</option>
					{foreach from=$gliederungen item=gliederung}<option value="{$gliederung.gliederungid|escape:html}">{$gliederung.label|escape:html}</option>{/foreach}
				</select>
				<table class="permissionlist" name="permissionlist" id="permissionlist_">
				{foreach from=$permissions_global item=permission}
					<tr class="permissionrow">
						<td><input type="checkbox" name="permissions[]" value="{$permission.permissionid|escape:html}"
							{foreach from=$rolepermissions item=rolepermission}
								{if $permission.permissionid == $rolepermission.permission.permissionid}checked="checked"{/if}{/foreach} /></td>
						<th>{$permission.label|__|escape:html}</th>
					</tr>
				{/foreach}
				</table>
				{foreach from=$gliederungen item=gliederung}
					<table class="permissionlist" name="permissionlist" id="permissionlist_{$gliederung.gliederungid}">
					{foreach from=$permissions_local item=permission}
						<tr class="permissionrow">
							<td><input type="checkbox" name="permissions[]" value="{$permission.permissionid|escape:html}-{$gliederung.gliederungid}"
								{foreach from=$rolepermissions item=rolepermission}
									{if $gliederung.gliederungid == $rolepermission.gliederung.gliederungid
									and $permission.permissionid == $rolepermission.permission.permissionid}checked="checked"{/if}{/foreach} /></td>
							<td><input type="checkbox" name="transitive_perms[]" value="{$permission.permissionid|escape:html}-{$gliederung.gliederungid}"
								{foreach from=$rolepermissions item=rolepermission}
									{if $gliederung.gliederungid == $rolepermission.gliederung.gliederungid
									and $permission.permissionid == $rolepermission.permission.permissionid
									and $rolepermission.transitive}checked="checked"{/if}{/foreach} /></td>
							<th>{$permission.label|__|escape:html}</th>
						</tr>
					{/foreach}
					</table>
				{/foreach}
				<button class="btn btn-primary submit" type="submit" name="savepermissions" value="1">{"Speichern"|__}</button>
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
{include file="footer.html.tpl"}
