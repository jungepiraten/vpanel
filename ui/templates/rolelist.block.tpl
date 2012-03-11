<table class="table table-condensed table-striped table-bordered">
	<thead>
		<tr>
			<th>#</th>
			<th>{"Name"|__}</th>
		</tr>
	</thead>
	{foreach from=$roles item=role}
		<tr onclick="doNav('{"roles_details"|___:$role.roleid}')">
			<td>{$role.roleid}</td>
			<td>
				{$role.label}
				{if $showroledel}
				<span class="close" id="{$user.userid}">&times;</span>
				<a class="delLink" style="display:none;" href="{"roles_del"|___:$role.roleid}">Soll die Rolle wirklich gelöscht werden?</a>
				{/if}
				{if $showuserdel}
				<span class="close" id="{$user.userid}">&times;</span>
				<a class="delLink" style="display:none;" href="{"users_delrole"|___:$user.userid}">Soll der Benutzer wirklich aus der Rolle gelöscht werden?</a>
				{/if}
				<p class="description">{$role.description}</p>
			</td>
		</tr>
	{/foreach}
</table>

{include file="deleteModal.block.tpl"}