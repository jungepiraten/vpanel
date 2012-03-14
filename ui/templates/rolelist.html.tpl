{include file="header.html.tpl" ansicht="Rollenverwaltung" menupunkt="role"}
<div class="buttonbox">
<a href="{"roles_create"|___}" class="btn btn-success">{"Neue Rolle"|__}</a>
</div>
{if count(roles) > 0}
{include file="rolelist.block.tpl" showroledel=1 roles=$roles}
{if count(roles) > 10}
<div class="buttonbox">
 <a href="{"roles_create"|___}" class="btn btn-success">{"Neue Rolle"|__}</a>
</div>
{/if}
{/if}
{include file="footer.html.tpl"}
