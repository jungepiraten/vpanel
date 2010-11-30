{include file="header.html.tpl" ansicht="Rollenverwaltung"}
<p class="pagetitle">Rollenverwaltung</p>
<a href="{"roles_create"|___}" class="neuset">{"Neue Rolle"|__}</a>
{include file="rolelist.block.tpl" showroledel=1 roles=$roles}
<a href="{"roles_create"|___}" class="neuset">{"Neue Rolle"|__}</a>
{include file="footer.html.tpl"}
