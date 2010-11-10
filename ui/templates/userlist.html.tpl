{include file=header.html.tpl}
<p class="pagetitle">Benutzerverwaltung</p>
<a href="{"users_create"|___}" class="neuset">{"Neuen Benutzer anlegen"|__}</a>
{include file=userlist.block.tpl users=$users}
<a href="{"users_create"|___}" class="neuset">{"Neuen Benutzer anlegen"|__}</a>
{include file=footer.html.tpl}
