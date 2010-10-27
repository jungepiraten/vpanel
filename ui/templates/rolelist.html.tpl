{include file=header.html.tpl}
<div class="roles">
{foreach from=$roles item=role}
<div class="role">
<a href="{"roles_details"|___:$role.roleid}" class="label">{$role.label}</a> <a href="{"roles_del"|___:$role.roleid}" class="delrole">{"entfernen"|__}</a>
<span class="description">{$role.description}</span>
</div>
{/foreach}
</div>
{include file=footer.html.tpl}
