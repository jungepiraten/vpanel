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
				<a href="{"roles_details"|___:$role.roleid}">{$role.label}</a>
				{if $showroledel}
					<a class="close delete" href="{"roles_del"|___:$role.roleid}">&times;</a>
				{/if}
				{if $showuserdel}
					<a class="close" href="{"users_delrole"|___:$user.userid:$role.roleid}">&times;</a>
				{/if}
			</td>
		</tr>
	{/foreach}
</table>
