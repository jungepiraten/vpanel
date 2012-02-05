{include file="header.html.tpl" ansicht="Benutzerverwaltung"}
<p class="pagetitle">Benutzerverwaltung</p>
<div class="buttonbox">
 <a href="{"users_create"|___}" class="neuset">{"Neuen Benutzer anlegen"|__}</a>
</div>
{include file="userlist.block.tpl" users=$users showuserdel=1}
<div class="buttonbox">
 <a href="{"users_create"|___}" class="neuset">{"Neuen Benutzer anlegen"|__}</a>
</div>
{include file="footer.html.tpl"}
