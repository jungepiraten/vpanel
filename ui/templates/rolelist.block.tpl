<table class="table table-condensed table-striped table-bordered">
	<thead>
		<tr>
			<th>
				#
			</th>
			<th>
				{"Name"|__}
			</th>
		</tr>
	</thead>
	{foreach from=$roles item=role}
		<tr onclick="doNav('{"roles_details"|___:$role.roleid}')">
			<td>{$role.roleid}</td>
			<td>
				{$role.label}
				{if $showroledel}
				<span class="close" id="{$user.userid}">&times;</span>
				<a id="delLink" style="display:none;" href="{"roles_del"|___:$role.roleid}">Soll die Rolle wirklich gelöscht werden?</a>
				{/if}
				{if $showuserdel}
				<span class="close" id="{$user.userid}">&times;</span>
				<a id="delLink" style="display:none;" href="{"users_delrole"|___:$user.userid}">Soll der Benutzer wirklich aus der Rolle gelöscht werden?</a>
				{/if}
				<p class="description">{$role.description}</p>
			</td>
		</tr>
	{/foreach}
</table>

<div class="modal fade" id="delRoleModal">
		  <div class="modal-header">
		    <a class="close" data-dismiss="modal">×</a>
		    <h3>Achtung:</h3>
		  </div>
		  <div class="modal-body">
		    <p></p>
		  </div>
		  <div class="modal-footer">
		    <a class="btn btn-danger" id="delRole">Löschen</a>
		    <a class="btn"  data-dismiss="modal">Abbrechen</a>
		  </div>
</div>
<script type="text/javascript">
{literal}
	$("span.close").click(function() {
		event.stopImmediatePropagation();
		var textString = $("a#delLink").html()
		$("#delRoleModal").children(".modal-body").children("p").text(textString);
		$("#delRoleModal").modal();
	});
	$("#delRole").click(function() {
		document.location.href = $("a#delLink").attr("href");
	});
{/literal}
</script>