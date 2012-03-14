<div class="btn-group">
	<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
		{"Aktionen"|__}
		<span class="caret"></span>
	</a>
	<ul class="dropdown-menu">
		<li>
		 <a class="closePopupTrigger">{"Löschen"|__}</a>
		 <a class="delLink" style="display:none;" href="{"mitglieder_del"|___:$mitglied.filterid}">Soll der Benutzer wirklich gelöscht werden?</a>
		</li>
		<li><a href="{"mitglieder_sendmail.select"|___:$filterid}">{"Mails verschicken"|__}</a></li>
		<li><a href="{"mitglieder_export.options"|___:$filterid}">{"Exportieren"|__}</a></li>
		<li><a href="{"mitglieder_statistik.start"|___:$filterid}">{"Statistik erzeugen"|__}</a></li>
		<li><a href="{"mitglieder_setbeitrag.selectbeitrag"|___:$filterid}">{"Beitrag eintragen"|__}</a></li>
	</ul>
</div>
{include file="deleteModal.block.tpl"}

