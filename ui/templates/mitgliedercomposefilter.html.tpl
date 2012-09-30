{include file="header.html.tpl" ansicht="Filter erstellen" menupunkt="mitglied"}
{literal}
<style type="text/css">
.filter, .filter_wildcard, .filter_chooser
	{border:2px solid black; margin:0.5em; padding:0.5em;}
.filter_chooser
	{border-style:dashed;}
.filter_chooser>ul
	{list-style:none; margin:0; padding:0;}
</style>
{/literal}

 <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.4/leaflet.css" />
 <!--[if lte IE 8]>
  <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.4/leaflet.ie.css" />
 <![endif]-->
 <script src="http://cdn.leafletjs.com/leaflet-0.4/leaflet.js"></script>
<script type="text/javascript">

var presetFilters = new Array();
{foreach from=$filters item=filter}
presetFilters[{$filter.filterid}] = "{$filter.label}";
{/foreach}

var umkreisMaps = new Array();
var umkreisMapsLayer = new Array();

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

function checkUmkreisMap(id, content) {
	if ($("#map_"+id).size() <= 0) {
		content.append($("<div>").prop("id","map_"+id).css("width","600px").css("height","250px"));
		umkreisMaps[id] = new L.Map("map_"+id);
		var osm = new L.TileLayer("http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",{minZoom: 4, maxZoom:18, attribution: "Map data © openstreetmap contributors"});
		umkreisMaps[id].setView(new L.LatLng(50.111667, 8.685833),6);
		umkreisMaps[id].addLayer(osm);
		umkreisMapsLayer[id] = new L.LayerGroup();
		umkreisMaps[id].addLayer(umkreisMapsLayer[id]);
		umkreisMaps[id].on("click", function(e) {
			$('input[name="filter['+id+'][lat]"]').val(e.latlng.lat);
			$('input[name="filter['+id+'][lng]"]').val(e.latlng.lng);
			updateUmkreisMap(id);
		});
	}
}

function updateUmkreisMap(id) {
	var lat = $('input[name="filter['+id+'][lat]"]').val();
	var lng = $('input[name="filter['+id+'][lng]"]').val();
	var radius = $('input[name="filter['+id+'][radius]"]').val();

	var latlng = new L.LatLng(lat,lng);
	umkreisMaps[id].panTo(latlng);
	umkreisMapsLayer[id].clearLayers();
	umkreisMapsLayer[id].addLayer(new L.Circle(latlng, radius * 1000, {color:'red',fillColor:'#f03',fillOpacity:0.5}));
}

function generateFilterChooser(id, parentID) {
	return $("<div>").addClass("filter_chooser").append($("<ul>").append(
		$("<li>").append($("<a>").prop("href","javascript:setFilter('"+id+"', '"+parentID+"', 'and')").append("AND")),
		$("<li>").append($("<a>").prop("href","javascript:setFilter('"+id+"', '"+parentID+"', 'or')").append("OR")),
		$("<li>").append($("<a>").prop("href","javascript:setFilter('"+id+"', '"+parentID+"', 'not')").append("NOT")),
		$("<li>").append($("<a>").prop("href","javascript:setFilter('"+id+"', '"+parentID+"', 'preset')").append("Vordefiniert")),
		$("<li>").append($("<a>").prop("href","javascript:setFilter('"+id+"', '"+parentID+"', 'eintrittafter')").append("Eingetreten nach")),
		$("<li>").append($("<a>").prop("href","javascript:setFilter('"+id+"', '"+parentID+"', 'austrittafter')").append("Ausgetreten nach")),
		$("<li>").append($("<a>").prop("href","javascript:setFilter('"+id+"', '"+parentID+"', 'age')").append("&Auml;lter als")),
		$("<li>").append($("<a>").prop("href","javascript:setFilter('"+id+"', '"+parentID+"', 'eintrittage')").append("Zum Eintritt &auml;lter als")),
		$("<li>").append($("<a>").prop("href","javascript:setFilter('"+id+"', '"+parentID+"', 'search')").append("Suchen nach")),
		$("<li>").append($("<a>").prop("href","javascript:setFilter('"+id+"', '"+parentID+"', 'umkreis')").append("Umkreissuche"))
	));
}

function generateFilterBorder(id, parentID) {
	return $("<div>").prop("id", "filter_" + id).append(generateFilterChooser(id, parentID));
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
	case "eintrittafter":
		content.append("Eingetreten nach");
		content.append($("<input>").prop("type","text").prop("name","filter["+id+"][timestamp]"));
		break;
	case "austrittafter":
		content.append("Ausgetreten nach");
		content.append($("<input>").prop("type","text").prop("name","filter["+id+"][timestamp]"));
		break;
	case "age":
		content.append("&Auml;lter als");
		content.append($("<input>").prop("type","text").prop("name","filter["+id+"][age]"));
		break;
	case "eintrittage":
		content.append("Zum Eintritt &auml;lter als");
		content.append($("<input>").prop("type","text").prop("name","filter["+id+"][age]"));
		break;
	case "search":
		content.append("Suchen nach");
		content.append($("<input>").prop("type","text").prop("name","filter["+id+"][query]"));
		break;
	case "umkreis":
		content.append("Umkreissuche");
		var input_lat = $("<input>").prop("type","text").prop("name","filter["+id+"][lat]").prop("size","8").attr('placeholder', 'lat');
		input_lat.focus(function() {checkUmkreisMap(id, content);}).blur(function() {checkUmkreisMap(id,content);}).keyup(function() {updateUmkreisMap(id);});
		content.append(input_lat);

		var input_lng = $("<input>").prop("type","text").prop("name","filter["+id+"][lng]").prop("size","8").attr('placeholder', 'lng');
		input_lng.focus(function() {checkUmkreisMap(id, content);}).blur(function() {checkUmkreisMap(id,content);}).keyup(function() {updateUmkreisMap(id);});
		content.append(input_lng);

		var input_radius = $("<input>").prop("type","text").prop("name","filter["+id+"][radius]").prop("size","4").val("25");
		input_radius.focus(function() {checkUmkreisMap(id, content);}).blur(function() {checkUmkreisMap(id,content);}).keyup(function() {updateUmkreisMap(id);});
		content.append(input_radius);
		content.append("km");
		break;
	}

	var filteroptions = $("<div>").addClass("filteroptions").append( $("<a>").prop("href", "javascript:unsetFilter('"+id+"', '"+parentID+"')").addClass("close").append("&times;") );
	return $("<div>").addClass("filter filter_" + type).append(filteroptions).append(content);
}

function showFilterChooser(id, parentID) {
	setFilterPane(id, generateFilterChooser(id, parentID));
}

function setFilterPane(id, pane) {
	$("#filter_" + id).empty().append(pane);
}

function setFilter(id, parentID, type) {
	var filter = $("#filter_" + id);
	setFilterPane(id, generateFilter(id, parentID, type));
	if (filter.parent().hasClass("combined")) {
		filter.parent().append(generateFilterBorder(generateID(), parentID));
	}
}

function unsetFilter(id, parentID) {
	var filter = $("#filter_" + id);
	if (filter.parent().hasClass("combined")) {
		filter.remove();
	} else {
		filter.empty().append(generateFilterChooser(id, parentID));
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
  <input type="submit" name="generate" value="Start" class="btn btn-primary" />
 </fieldset>
</form>
{include file="footer.html.tpl"}
