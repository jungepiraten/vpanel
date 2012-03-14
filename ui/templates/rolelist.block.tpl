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
				<span class="close closePopupTrigger" id="{$user.userid}">&times;</span>
				<a class="delLink" style="display:none;" href="{"roles_del"|___:$role.roleid}">Soll die Rolle wirklich gelöscht werden?</a>
				{/if}
				{if $showuserdel}
				<a class="close" href="{"users_delrole"|___:$user.userid:$role.roleid}">&times;</a>
				{/if}
			</td>
		</tr>
	{/foreach}
</table>

{include file="deleteModal.block.tpl"}
