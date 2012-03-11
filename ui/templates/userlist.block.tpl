<table class="table table-striped table-bordered table-condensed">
	<thead>
		<tr>
			<th>#</th>
			<th>Name</th>
		</tr>
	</thead>
	{foreach from=$users item=user}
	<a href="{"users_details"|___:$user.userid}">
		<tr>
			<td>{$user.userid}</td>
			<td>
				<div style="float:left">{$user.username}</div>
				{if $showuserdel}
				<span class="close" id="{$user.userid}">&times;</span>
				{/if}
				{if $showroledel}
				<span class="close" id="{$user.userid}">&times;</span>
				{/if}
				<div style="clear:both;"></div>
			</td>
		</tr>
		</a>

		
	{/foreach}
</table>
<script type="text/javascript">
{literal}
	var userID = "";
	$("span.close").click(function() {
		userID = {/literal}{$user.userid}{literal};
		$("#delUserModal").modal();
	});
	$("#delUser").click(function() {

	});
{/literal}
</script>
<div class="modal fade" id="delUserModal">
		  <div class="modal-header">
		    <a class="close" data-dismiss="modal">×</a>
		    <h3>Achtung:</h3>
		  </div>
		  <div class="modal-body">
		    <p>Soll der Benutzer wirklich gelöscht werden?</p>
		  </div>
		  <div class="modal-footer">
		    <a class="btn btn-danger" id="delUser">Löschen</a>
		    <a class="btn"  data-dismiss="modal">Abbrechen</a>
		  </div>
</div>