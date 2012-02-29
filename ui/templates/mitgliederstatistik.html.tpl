{include file="header.html.tpl" ansicht="Mitgliederstatistik"}
<h1 class="pagetitle">Mitgliederstatistik</h1>
{if isset($statistik.agegraphfile)}<img src="{"mitglieder_statistik_get_agegraph"|___:$statistik.statistikid}" /><br />{/if}
{if isset($statistik.timegraphfile)}<img src="{"mitglieder_statistik_get_timegraph"|___:$statistik.statistikid}" /><br />{/if}
{if isset($statistik.timebalancegraphfile)}<img src="{"mitglieder_statistik_get_timebalancegraph"|___:$statistik.statistikid}" /><br />{/if}
{include file="footer.html.tpl"}
