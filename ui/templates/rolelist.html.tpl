{include file="header.html.tpl" ansicht="Rollenverwaltung"}
<div class="buttonbox">
 <a href="{"roles_create"|___}" class="btn btn-primary">{"Neue Rolle"|__}</a>
</div>
{include file="rolelist.block.tpl" showroledel=1 roles=$roles}
{if count(roles) > 10}
<div class="buttonbox">
 <a href="{"roles_create"|___}" class="btn btn-primary">{"Neue Rolle"|__}</a>
</div>
{/if}
{include file="footer.html.tpl"}
