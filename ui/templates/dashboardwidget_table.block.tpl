<table class="table table-condensed table-striped table-hover" id="widgettable-{$widget.widgetid}">
	<thead>
	</thead>
	<tbody>
	</tbody>
</table>
<script type="text/javascript">
{literal}
function initWidgetTable_{/literal}{$widget.widgetid}{literal}(options) {
	/** Before you kill me: I CAN EXPLAIN THIS!
	 * As this code is inserted into the DOM after the document has been loaded (so $(..) will execute immedately)
	 * and obviously executed before this DOM-part is fully inserted (so you cannot simply use the table in dashboardwidget_table.block.tpl),
	 * we need some way to execute code at the right time. 100 ms should be fair btw
	 **/
	setTimeout(function () {
		var table = $("#widgettable-{/literal}{$widget.widgetid}{literal}");
		table.children("thead").empty();
		var headRow = $("<tr>");
		for (var i in options.fields) {
			headRow.append($("<th>").text(i));
		}
		table.children("thead").append(headRow);
		var tbody = table.children("tbody");
		$.post(options.url, options.data || {}, function (res) {
			tbody.empty();
			for (var i in res) {
				var row = $("<tr>");
//				row.click(function() {location.href = $(this).find("a").attr("href");}).css("cursor", "pointer");
				for (var f in options.fields) {
					row.append($("<td>").append(options.fields[f](res[i])));
				}
				tbody.append(row);
			}
			if (options.reload) {
				window.setTimeout(function() {initWidgetTable_{/literal}{$widget.widgetid}{literal}(options)}, options.reload * 1000);
			}
		}, "json");
	});
}
{/literal}
</script>
