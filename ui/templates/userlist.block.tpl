<ul class="entrylist">
{foreach from=$users item=user}
<li class="entry{cycle values="odd,even"}">
{if $showuserdel}
<a href="{"users_del"|___:$user.userid}" class="delimg" title="{"Benutzer loeschen"|__}" onClick="return confirm('Benutzer wirklich löschen?');">&nbsp;</a>
{/if}
{if $showroledel}
<a href="{"roles_deluser"|___:$roleid:$user.userid}" class="delimg" title="{"Rolle entfernen"|__}" onClick="return confirm('Rolle wirklich löschen?');">&nbsp;</a>
{/if}
<span class="separator"> | </span>
<a href="{"users_details"|___:$user.userid}">{$user.username}</a>
</li>
{/foreach}
</ul>
