{include file="header.html.tpl" ansicht="Mitgliederstatistik" menupunkt="mitglied"}
{if isset($statistik.agegraphfile)}<img src="{"mitglieder_statistik_get_agegraph"|___:$statistik.statistikid}" /><br />{/if}
{if isset($statistik.timegraphfile)}<img src="{"mitglieder_statistik_get_timegraph"|___:$statistik.statistikid}" /><br />{/if}
{if isset($statistik.timebalancegraphfile)}<img src="{"mitglieder_statistik_get_timebalancegraph"|___:$statistik.statistikid}" /><br />{/if}
{if isset($statistik.gliederungchartfile)}<img src="{"mitglieder_statistik_get_gliederungchart"|___:$statistik.statistikid}" /><br />{/if}
{if isset($statistik.gliederungchartfile)}<img src="{"mitglieder_statistik_get_statechart"|___:$statistik.statistikid}" /><br />{/if}
{if isset($statistik.mitgliedschaftchartfile)}<img src="{"mitglieder_statistik_get_mitgliedschaftchart"|___:$statistik.statistikid}" /><br />{/if}
{include file="footer.html.tpl"}
