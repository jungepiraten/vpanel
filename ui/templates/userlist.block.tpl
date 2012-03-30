<table class="table table-striped table-bordered table-condensed">
	<thead>
		<tr>
			<th>#</th>
			<th>Name</th>
		</tr>
	</thead>
	{foreach from=$users item=user}
		<tr class="userTr" onclick="doNav('{"users_details"|___:$user.userid}')">
			<td>{$user.userid}</td>
			<td>
				{$user.username}
				{if $showuserdel}
					<a class="close delete" href="{"users_del"|___:$user.userid}">&times;</span>
				{/if}
				{if $showroledel}
					<a href="{"users_delrole"|___:$user.userid:$role.roleid}" class="close">&times;</a>
				{/if}
			</td>
		</tr>	
	{/foreach}
</table>
