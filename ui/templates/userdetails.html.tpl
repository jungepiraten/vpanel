{capture assign="ansicht"}
Benutzer &raquo;{$user.username|escape:html}&laquo; bearbeiten.
{/capture}
{include file="header.html.tpl" ansicht=$ansicht}
<div class="row-fluid">
	<div class="span6">
		<h2>{"Benutzerdaten"|__}</h2>
		{include file="userform.block.tpl" user=$user}
	</div>
	<div class="span6">
		<h2>Rollen</h2>
		{include file="rolelist.block.tpl" showuserdel=1 userid=$user.userid roles=$userroles}
		<form action="{"users_addrole"|___:$user.userid}" method="post" class="useraddrole">
			<fieldset>
		 		<input type="hidden" name="redirect" value="{$smarty.server.REQUEST_URI}" />
		 		<select name="roleid">
				{foreach from=$roles item=role}
					<option value="{$role.roleid|escape:html}">{$role.label|escape:html}</option>
				{/foreach}
		  		</select>
		  		<input class="submit" type="submit" name="do" value="{"Hinzufuegen"|__}" />
			</fieldset>
		</form>
	</div>
</div>
{include file="footer.html.tpl"}
