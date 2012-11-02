<table class="table table-condensed table-striped table-hover" id="widgettable-{$widget.widgetid}">
	<thead>
		<tr>
			<th>Uhrzeit</th>
			<th>Benutzer</th>
			<th>Dokument</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
<script type="text/javascript">
{literal}
$.get("{/literal}{"dokumente_timeline_json"|___}{literal}", function (res) {
	var tbody = $("#widgettable-{/literal}{$widget.widgetid}{literal} tbody");
	tbody.empty();
	for (var i in res) {
		var timestamp = new Date(1000 * res[i].timestamp).toISOString();
		tbody.append($("<tr>")
			.click(function() {location.href = $(this).find("a").attr("href");}).css("cursor", "pointer")
			.append(
				$("<td>").append($("<time>").attr("datetime", timestamp).attr("title", timestamp).timeago()),
				$("<td>").text(res[i].username),
				$("<td>").append($("<a>").attr("href",res[i].location).text(res[i].dokumentidentifier + " " + res[i].dokumentlabel))
			)
		);
	}
}, "json");
{/literal}
</script>
