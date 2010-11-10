<ul class="entrylist">
{foreach from=$users item=user}
<li class="entry{cycle values="odd,even"}">
<a href="{"users_del"|___:$user.userid}" class="delimg" title="Benutzer löschen">&nbsp;</a>
<span class="separator"> | </span>
<a href="{"users_details"|___:$user.userid}"">{$user.username}</a>
</li>
{/foreach}
</ul>
