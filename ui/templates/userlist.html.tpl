{include file="header.html.tpl" ansicht="Benutzerverwaltung" menupunkt="user"}
<div class="buttonbox">
 <a href="{"users_create"|___}" class="btn btn-success">{"Neuen Benutzer anlegen"|__}</a>
</div>
{if count(users) > 0}
{include file="userlist.block.tpl" users=$users showuserdel=1}
{if count(users) > 10}
<div class="buttonbox">
 <a href="{"users_create"|___}" class="btn btn-success">{"Neuen Benutzer anlegen"|__}</a>
</div>
{/if}
{/if}
{include file="footer.html.tpl"}
