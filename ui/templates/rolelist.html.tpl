{include file="header.html.tpl" ansicht="Rollenverwaltung"}
<p class="pagetitle">Rollenverwaltung</p>
<div class="buttonbox">
 <a href="{"roles_create"|___}" class="neuset">{"Neue Rolle"|__}</a>
</div>
{include file="rolelist.block.tpl" showroledel=1 roles=$roles}
<div class="buttonbox">
 <a href="{"roles_create"|___}" class="neuset">{"Neue Rolle"|__}</a>
</div>
{include file="footer.html.tpl"}
