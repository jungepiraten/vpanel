{include file="header.html.tpl" ansicht="Filter erstellen"}
<p class="pagetitle">Filter erstellen</p>

{literal}
<style type="text/css">
.filterchooser
	{position:absolute; z-index:7; width:200px; margin:auto; overflow:auto; background-color: white; border:2px solid black; border-radius:20px; padding:10px;}
.hidebackground
	{position:absolute; top:0px; left:0px; width:100%; height:100%; background-color:black; opacity:0.8;}
.filteroptions>.delimg
	{float:right;}

.filter, .filter_wildcard
	{border:2px solid black; margin:3px; padding:2px;}
.filter_wildcard
	{border-style:dashed;}
</style>
{/literal}

<script type="text/javascript">

var presetFilters = new Array();
{foreach from=$filters item=filter}
presetFilters[{$filter.filterid}] = "{$filter.label}";
{/foreach}

{literal}
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

function generateFilterWildcard(id, parentID) {
	return $("<div>").addClass("filter filter_wildcard").append($("<a>").prop("href", "javascript:showFilterChooser('"+id+"', '"+parentID+"');").append("Filter einfügen"));
}

function generateFilterBorder(id, parentID) {
	return $("<div>").prop("id", "filter_" + id).append(generateFilterWildcard(id, parentID));
}

function generateFilter(id, parentID, type) {
	var content = $("<div>");
	if (parentID != "") {
		content.append($("<input>").prop("type","hidden").prop("name","filter["+parentID+"][childs][]").prop("value",id));
	}
	content.append($("<input>").prop("type","hidden").prop("name","filter["+id+"][type]").prop("value",type));
	switch (type) {
	case "and":
		content.addClass("combined");
		content.append("AND");
		content.append(generateFilterBorder(generateID(), id));
		break;
	case "or":
		content.addClass("combined");
		content.append("OR");
		content.append(generateFilterBorder(generateID(), id));
		break;
	case "not":
		content.append("NOT");
		content.append(generateFilterBorder(generateID(), id));
		break;
	case "preset":
		var dropdown = $("<select>").prop("name","filter["+id+"][filterid]");
		presetFilters.forEach(function(value, index, filters) {
				dropdown.append($("<option>").prop("value",index).append(value));
			});
		content.append(dropdown);
		break;
	case "search":
		content.append("Suchen nach");
		content.append($("<input>").prop("type","text").prop("name","filter["+id+"][query]"));
		break;
	}

	var filteroptions = $("<div>").addClass("filteroptions").append( $("<a>").prop("href", "javascript:unsetFilter('"+id+"', '"+parentID+"')").addClass("delimg").append("&nbsp;") );
	return $("<div>").addClass("filter filter_" + type).append(filteroptions).append(content);
}

var hidebackground, filterchooser;

function showFilterChooser(id, parentID) {
	hidebackground = $("<div>").addClass("hidebackground");
	filterchooser = $("<div>").addClass("filterchooser").append($("<ul>").append(
				$("<li>").append($("<a>").prop("href","javascript:setFilter('"+id+"', '"+parentID+"', 'and')").append("AND")),
				$("<li>").append($("<a>").prop("href","javascript:setFilter('"+id+"', '"+parentID+"', 'or')").append("OR")),
				$("<li>").append($("<a>").prop("href","javascript:setFilter('"+id+"', '"+parentID+"', 'not')").append("NOT")),
				$("<li>").append($("<a>").prop("href","javascript:setFilter('"+id+"', '"+parentID+"', 'preset')").append("Vordefiniert")),
				$("<li>").append($("<a>").prop("href","javascript:setFilter('"+id+"', '"+parentID+"', 'search')").append("Suchen nach"))
			));
	$("body").append(hidebackground);
	$("body").append(filterchooser.fadeIn(700));
}

function hideFilterChooser() {
	filterchooser.remove();
	hidebackground.remove();
}

function setFilter(id, parentID, type) {
	hideFilterChooser();

	var filter = $("#filter_" + id);
	$("#filter_" + id).empty().append(generateFilter(id, parentID, type));
	if (filter.parent().hasClass("combined")) {
		filter.parent().append(generateFilterBorder(generateID(), parentID));
	}
}

function unsetFilter(id, parentID) {
	var filter = $("#filter_" + id);
	if (filter.parent().hasClass("combined")) {
		filter.remove();
	} else {
		filter.empty().append(generateFilterWildcard(id, parentID));
	}
}

$(function () {
	$("#filter").append(generateFilterBorder("matcher", ""));
});

</script>
{/literal}

<form action="{"mitglieder_composefilter"|___}" method="post">
 <fieldset>
  <div id="filter"></div>
  <input type="submit" name="generate" value="Start" />
 </fieldset>
</form>
{include file="footer.html.tpl"}
