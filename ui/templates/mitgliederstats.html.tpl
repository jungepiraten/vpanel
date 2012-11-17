{include file="header.html.tpl" ansicht="Mitgliederstatistik" menupunkt="mitglied"}
{foreach from=$tempfiles item=tempfile}
<img src="{"tempfile_get"|___:$tempfile.tempfileid}" /><br />
{/foreach}
{include file="footer.html.tpl"}
