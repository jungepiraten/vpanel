{include file="dashboardwidget_table.block.tpl"}
<script type="text/javascript">
{literal}
initWidgetTable_{/literal}{$widget.widgetid}{literal}({
	url: "{/literal}{"processes_timeline_json"|___}{literal}",
	fields: {
		"Status": function (res) {
			if (res.iswaiting) {
				return "Warte...";
			}
			if (res.isrunning) {
				return (100 * res.progress) + " %";
			}
			if (res.isfinished) {
				return "Abgeschlossen";
			}
		},
		"Start": function (res) {
			if (res.started == "") {
				return "";
			}
			var timestamp = new Date(1000 * res.started).toISOString();
			return $("<time>").attr("datetime", timestamp).attr("title", timestamp).timeago();
		},
		"Ende": function (res) {
			if (res.finished == "") {
				return "";
			}
			var timestamp = new Date(1000 * res.finished).toISOString();
			return $("<time>").attr("datetime", timestamp).attr("title", timestamp).timeago();
		},
		"Benutzer": function (res) {return (res.username ? res.username : $("<span>").css("color","#cccccc").text("(kein)"));},
		"Typ": function (res) {return res.type;},
	},
	{/literal}{if isset($reload)}reload: {$reload}{/if}{literal}
});
{/literal}
</script>
