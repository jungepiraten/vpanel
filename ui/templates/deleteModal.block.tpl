
<div class="modal fade" id="delModal">
		  <div class="modal-header">
		    <a class="close" data-dismiss="modal">×</a>
		    <h3>Achtung:</h3>
		  </div>
		  <div class="modal-body">
		    <p></p>
		  </div>
		  <div class="modal-footer">
		    <a class="btn btn-danger" id="delButton">Löschen</a>
		    <a class="btn"  data-dismiss="modal">Abbrechen</a>
		  </div>
</div>
<script type="text/javascript">
{literal}
	$("span.close").click(function() {
		event.stopImmediatePropagation();
		var textString = $(this).parent().children("a.delLink").html()
		var linkString = $(this).parent().children("a.delLink").attr("href")
		$("#delModal").children(".modal-body").children("p").text(textString);
		$("#delModal").children(".modal-footer").children("#delButton").attr("href", linkString);
		$("#delModal").modal();
	});
	$("#delRole").click(function() {
		document.location.href = $("a#delLink").attr("href");
	});
{/literal}
</script>