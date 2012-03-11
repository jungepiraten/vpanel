{include file="header.html.tpl" ansicht="Benutzerverwaltung"}
<div class="buttonbox">
 <a href="{"users_create"|___}" class="btn btn-primary">{"Neuen Benutzer anlegen"|__}</a>
</div>
{include file="userlist.block.tpl" users=$users showuserdel=1}
{if count(users) > 10}
<div class="buttonbox">
 <a href="{"users_create"|___}" class="btn btn-primary">{"Neuen Benutzer anlegen"|__}</a>
</div>
{/if}
{include file="footer.html.tpl"}
