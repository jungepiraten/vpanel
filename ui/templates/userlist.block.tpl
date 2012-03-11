<table class="table table-striped table-bordered table-condensed">
	<thead>
		<tr>
			<th>#</th>
			<th>Name</th>
		</tr>
	</thead>
	{foreach from=$users item=user}
		<tr onclick="doNav('{"users_details"|___:$user.userid}')">
			<td>{$user.userid}</td>
			<td>
				<div style="float:left">{$user.username}</div>
				{if $showuserdel}
				<span class="close" id="{$user.userid}">&times;</span>
				<a id="delLink" style="display:none;" href="{"users_del"|___:$user.userid}">Soll der Benutzer wirklich gelöscht werden?</a>
				{/if}
				{if $showroledel}
				<span class="close" id="{$user.userid}">&times;</span>
				<a id="delLink" style="display:none;" href="{"users_delrole"|___:$user.userid}">Soll der Benutzer wirklich aus der Rolle gelöscht werden?</a>
				{/if}
				<div style="clear:both;"></div>
			</td>
		</tr>	
	{/foreach}
</table>
<div class="modal fade" id="delUserModal">
		  <div class="modal-header">
		    <a class="close" data-dismiss="modal">×</a>
		    <h3>Achtung:</h3>
		  </div>
		  <div class="modal-body">
		    <p></p>
		  </div>
		  <div class="modal-footer">
		    <a class="btn btn-danger" id="delUser">Löschen</a>
		    <a class="btn"  data-dismiss="modal">Abbrechen</a>
		  </div>
</div>
<script type="text/javascript">
{literal}
	$("span.close").click(function() {
		event.stopImmediatePropagation();
		var textString = $("a#delLink").html()
		$("#delUserModal").children(".modal-body").children("p").text(textString);
		$("#delUserModal").modal();
	});
	$("#delUser").click(function() {
		document.location.href = $("a#delLink").attr("href");
	});
{/literal}
</script>