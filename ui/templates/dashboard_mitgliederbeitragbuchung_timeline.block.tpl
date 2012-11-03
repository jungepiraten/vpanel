{include file="dashboardwidget_table.block.tpl"}
<script type="text/javascript">
{literal}
initWidgetTable_{/literal}{$widget.widgetid}{literal}({
	url: "{/literal}{"mitglieder_beitraege_buchungen_timeline_json"|___}{literal}",
	fields: {
		"Uhrzeit": function (res) {
			var timestamp = new Date(1000 * res.timestamp).toISOString();
			return $("<time>").attr("datetime", timestamp).attr("title", timestamp).timeago();
		},
		"Benutzer": function (res) {return res.username;},
		"Beitrag": function (res) {return res.beitraglabel;},
		"Mitglied": function (res) {return $("<a>").attr("href",res.location).text(res.mitgliedlabel);},
		"Betrag": function (res) {return res.betrag;}
	},
	{/literal}{if isset($reload)}reload: {$reload}{/if}{literal}
});
{/literal}
</script>
