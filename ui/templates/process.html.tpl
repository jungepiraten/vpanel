{include file="header.html.tpl" ansicht="Prozessfortschritt"}
<p class="pagetitle">Prozessfortschritt</p>
<script type="text/javascript">
{literal}
$(document).ready(function () {
	$("#progressbar").progressBar({ barImage: "ui/images/progressbg.gif", boxImage: "ui/images/progressbar.gif", width: 300, height:30 });
});

function formatNumber(num) {
	num = Math.floor(num);
	if (num < 10) {
		num = "0" + num;
	}
	return num;
}

var eta = -1;
function countdownETA() {
	if (eta < 0) {
		$("#eta").html("");
		return;
	}
	eta = eta - 0.5;

	var seconds = eta;
	var minutes = Math.floor(seconds/60);
	seconds = seconds % 60;

	var hours = Math.floor(minutes/60);
	minutes = minutes % 60;
	$("#eta").html("ETA: " + hours + ":" + formatNumber(minutes) + ":" + formatNumber(seconds));
}

function updateBar(data) {
	if (data["isfinished"] && data["finishedpage"]) {
		$("#progresstest").html("Fertiggestellt");
		location.href = data["finishedpage"];
	}
	if (data["isrunning"]) {
		$("#progresstest").html("Prozess läuft");
	}
	if (data["iswaiting"]) {
		$("#progresstest").html("Warte auf Prozessbeginn");
	}
	$("#progressbar").progressBar(100 * data["progress"]);
	if (data["eta"]) {
		eta = data["eta"];
	} else {
		eta = -1;
	}
}

function queryProgress() {
	$.post("{/literal}{"processes_json"|___}{literal}",{
				processid: {/literal}{$process.processid}{literal}
			}, updateBar,'json');
}

setTimeout(queryProgress, 100);
setInterval(queryProgress, 5000);
setInterval(countdownETA, 500);
{/literal}
</script>
<div id="progressbar">{$process.progress*100}%</div>
<p id="eta"></p>
<p id="progresstext">{if $process.iswaiting}Warte ...{/if}</p>
{include file="footer.html.tpl"}
