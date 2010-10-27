{include file=header.html.tpl}
<ul class="users">
{foreach from=$users item=user}
 <li class="user"><a href="{"users_details"|___:$user.userid}">{$user.username}</a> <a href="{"users_del"|___:$user.userid}" class="deluser">{"entfernen"|__}</a></li>
{/foreach}
</ul>
{include file=footer.html.tpl}
