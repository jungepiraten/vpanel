<div class="pagination" id="widgetpagination-{$widget.widgetid}">
	<ul>
		<li class="prev"><a href="#">&lt;</a></li>
		<li class="cur disabled active"><a href="#">&nbsp;</a></li>
		<li class="next"><a href="#">&gt;</a></li>
	</ul>
</div>
<table class="table table-condensed table-striped table-hover" id="widgettable-{$widget.widgetid}">
	<thead>
	</thead>
	<tbody>
	</tbody>
</table>
<script type="text/javascript">
{literal}
var widgetOptions_{/literal}{$widget.widgetid}{literal} = {
	page:0,
	timeout:0
};

function initWidgetTable_{/literal}{$widget.widgetid}{literal}(options) {
	/** Before you kill me: I CAN EXPLAIN THIS!
	 * As this code is inserted into the DOM after the document has been loaded (so $(..) will execute immedately)
	 * and obviously executed before this DOM-part is fully inserted (so you cannot simply use the table in dashboardwidget_table.block.tpl),
	 * we need some way to execute code at the right time. 100 ms should be fair btw
	 **/
	setTimeout(function () {
		if (widgetOptions_{/literal}{$widget.widgetid}{literal}.timeout > 0) {
			window.clearTimeout(widgetOptions_{/literal}{$widget.widgetid}{literal}.timeout);
		}
		var table = $("#widgettable-{/literal}{$widget.widgetid}{literal}");
		table.children("thead").empty();
		var headRow = $("<tr>");
		for (var i in options.fields) {
			headRow.append($("<th>").text(i));
		}
		table.children("thead").append(headRow);
		var tbody = table.children("tbody");
		$.post(options.url, options.data || { page: widgetOptions_{/literal}{$widget.widgetid}{literal}.page }, function (res) {
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
				widgetOptions_{/literal}{$widget.widgetid}{literal}.timeout = window.setTimeout(function() {initWidgetTable_{/literal}{$widget.widgetid}{literal}(options)}, options.reload * 1000);
			}
		}, "json");

		var pagination = $("#widgetpagination-{/literal}{$widget.widgetid}{literal}");
		pagination.find(".cur a").text(widgetOptions_{/literal}{$widget.widgetid}{literal}.page + 1);
		pagination.find(".prev").unbind("click").toggleClass("disabled", widgetOptions_{/literal}{$widget.widgetid}{literal}.page <= 0).click(function () {
			widgetOptions_{/literal}{$widget.widgetid}{literal}.page--;
			initWidgetTable_{/literal}{$widget.widgetid}{literal}(options);
			return false;
		});
		pagination.find(".next").unbind("click").click(function () {
			widgetOptions_{/literal}{$widget.widgetid}{literal}.page++;
			initWidgetTable_{/literal}{$widget.widgetid}{literal}(options);
			return false;
		});
	});
}
{/literal}
</script>
