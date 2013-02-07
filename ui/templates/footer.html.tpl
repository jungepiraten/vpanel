        {if $session->isSignedIn()}
		</div>
	</div>
	{else}
	</div>
	{/if}
</div>

<hr />

<div class="modal fade" id="delModal">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">×</a>
		<h3>Achtung</h3>
	</div>
	<div class="modal-body">
		<p></p>
	</div>
	<div class="modal-footer">
		<a class="btn btn-danger" id="delButton">Löschen</a>
		<a class="btn" data-dismiss="modal">Abbrechen</a>
	</div>
</div>
{literal}
<script type="text/javascript">
$("tr").hover(
	function() {
		$(this).find(".close").stop().animate({"opacity": "0.5"}, "medium");
	},
	function() {
		$(this).find(".close").stop().animate({"opacity": "0.2"}, "medium");
	});
$(".delete").click(function() {
	event.stopImmediatePropagation();
	var textString = "Wirklich löschen?";
	var linkString = $(this).attr("href");
	$("#delModal").children(".modal-body").children("p").text(textString);
	$("#delModal").children(".modal-footer").children("#delButton").attr("href", linkString);
	$("#delModal").modal();
	return false;
});
$(".timestamp").timeago();
</script>
{/literal}
</body>
</html>
