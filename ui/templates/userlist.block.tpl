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
				<span class="close" id="{$user.userid}">&times;</span>
				<a class="delLink" style="display:none;" href="{"users_del"|___:$user.userid}">Soll der Benutzer wirklich gelöscht werden?</a>
				{/if}
				{if $showroledel}
				<span class="close" id="{$user.userid}">&times;</span>
				<a class="delLink" style="display:none;" href="{"users_delrole"|___:$user.userid}">Soll der Benutzer wirklich aus der Rolle gelöscht werden?</a>
				{/if}
				<div style="clear:both;"></div>
			</td>
		</tr>	
	{/foreach}
</table>

{include file="deleteModal.block.tpl"}