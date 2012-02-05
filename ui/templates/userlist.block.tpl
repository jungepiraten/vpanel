<ul class="entrylist">
{foreach from=$users item=user}
<li class="entry{cycle values="odd,even"}">
{if $showuserdel}
<a style="float:right; margin-top:7px;margin-right:7px;" href="{"users_del"|___:$user.userid}" class="delimg" title="{"Benutzer loeschen"|__}" onClick="return confirm('Benutzer wirklich löschen?');">&nbsp;</a>
{/if}
{if $showroledel}
<a style="float:right; margin-top:7px;margin-right:7px;" href="{"roles_deluser"|___:$roleid:$user.userid}" class="delimg" title="{"Rolle entfernen"|__}" onClick="return confirm('Rolle wirklich löschen?');">&nbsp;</a>
{/if}
<div style="float:left; margin-left:10px"><a href="{"users_details"|___:$user.userid}">{$user.username}</a><br />
<span class="description">&nbsp;</span></div>
<div style="clear:both;"></div>
</li>
{/foreach}
</ul>
