{include file="header.html.tpl" ansicht="Dashboard" menupunkt="dashboard"}
<form action="{"index"|___}" method="post">
	<div class="btn-group pull-right">
		<button onClick="addColumn();return false;" class="btn">Spalte hinzufügen</button>
		<button type="submit" name="addWidgets" class="btn btn-success">Speichern</button>
	</div>
	<div id="widgets"></div>
</form>
<style type="text/css">
{literal}
.widget
	{border:2px solid black; margin:0.5em; padding:0.5em;}
.newwidget
	{border-style:dashed;}
.newwidget>.content>ul
	{list-style:none; margin:0; padding:0;}
{/literal}
</style>
<script type="text/javascript">
{literal}

var maxColumn = 0;

function generateID() {
	var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
	var string_length = 8;
	var randomstring = '';
	for (var i=0; i<string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		randomstring += chars.substring(rnum,rnum+1);
	}
	return randomstring;
}

function addColumn(index) {
	if (index == undefined) {
		index = ++maxColumn;
	}
	maxColumn = Math.max(index, maxColumn);
	$("#widgets").append(
		$("<div>").attr("id", "column-" + index).css("float", "left").append($("<div>").addClass("widgets"))
	);

	addWidget(index);
}

function checkWidgets(column) {
	if ($("#widgets #column-" + column + " .newwidget").length == 0) {
		addWidget(column);
	}
}

function addWidget(column) {
	var id = generateID();
	$("#widgets #column-" + column).append(
		$("<div>").addClass("widget").addClass("widget-" + id).addClass("newwidget").append(
			$("<input>").attr("type","hidden").attr("name","widgets[" + id + "][column]").attr("value",column),
			$("<div>").addClass("content").append(
				$("<ul>").append(
					$("<li>").append($("<a>").click(function(){setWidget(id,"static");checkWidgets(column);}).append("Statisch")),
					$("<li>").append($("<a>").click(function(){setWidget(id,"mitgliederbeitragbuchung_timeline");checkWidgets(column);}).append("Beitragsbuchungen")),
					$("<li>").append($("<a>").click(function(){setWidget(id,"mitgliederrevision_timeline");checkWidgets(column);}).append("Mitglieder")),
					$("<li>").append($("<a>").click(function(){setWidget(id,"dokumentnotizen_timeline");checkWidgets(column);}).append("Dokumente"))
				)
			)
		)
	);
}

function setWidget(id, type) {
	var content = $("<div>").addClass("form-horizontal");
	switch (type) {
	case "static":
		content.append($("<textarea>").attr("name","widgets[" + id + "][text]"));
		break;
	case "mitgliederbeitragbuchung_timeline":
		content.append($("<div>").addClass("control-group").append(
			$("<label>").addClass("control-label").text("Reloadzeit in Sekunden"),
			$("<div>").addClass("controls").append(
				$("<input>").attr("type","text").attr("name","widgets[" + id + "][reload]").attr("placeholder","0 um zu deaktivieren")
			)
		));
		break;
	case "mitgliederrevision_timeline":
		content.append($("<div>").addClass("control-group").append(
			$("<label>").addClass("control-label").text("Reloadzeit in Sekunden"),
			$("<div>").addClass("controls").append(
				$("<input>").attr("type","text").attr("name","widgets[" + id + "][reload]").attr("placeholder","0 um zu deaktivieren")
			)
		));
		break;
	case "dokumentnotizen_timeline":
		content.append($("<div>").addClass("control-group").append(
			$("<label>").addClass("control-label").text("Reloadzeit in Sekunden"),
			$("<div>").addClass("controls").append(
				$("<input>").attr("type","text").attr("name","widgets[" + id + "][reload]").attr("placeholder","0 um zu deaktivieren")
			)
		));
		break;
	}
	$("#widgets .widget-" + id)
		.removeClass("newwidget")
		.append($("<input>").attr("type","hidden").attr("name","widgets["+id+"][type]").attr("value",type))
		.children(".content").empty().append(content);
}

{/literal}
</script>
<script type="text/javascript">
{literal}
var callbacks = [];
{/literal}

{foreach from=$columns key=index item=widgets}
addColumn({$index});
{foreach from=$widgets item=widget}
$.get("{"dashboard_widget_json"|___:$widget.widgetid}", function (res) {literal}{{/literal}
	$("#widgets #column-{$index} .widgets").append(
		$("<div>").addClass("widget").append(
			$("<a>").addClass("close").attr("href", "{"dashboard_widget_del"|___:$widget.widgetid}").html("×"),
			$("<div>").html(res)
		)
	);
{literal}});{/literal}
{/foreach}
{foreachelse}
addColumn();
{/foreach}
</script>
{include file="footer.html.tpl"}
