{include file="header.html.tpl" ansicht="Prozessfortschritt"}
<p class="pagetitle">Prozessfortschritt</p>
<script type="text/javascript">
{literal}
$(document).ready(function () {
	$("#progressbar").progressBar({ barImage: "ui/images/progressbg.gif", boxImage: "ui/images/progressbar.gif", width: 300, height:30 });
});

function updateBar(data) {
	$("#progressbar").progressBar(100*data["progress"]);
}

setInterval(function() {
	$.post("{/literal}{"processes_json"|___}{literal}",{
				processid: {/literal}{$process.processid}{literal}
			}, updateBar,'json');
			}, 5000);
{/literal}
</script>
<div id="progressbar">{$process.progress*100}%</div>
<p>{if $process.iswaiting}Warte ...{/if}
{include file="footer.html.tpl"}
