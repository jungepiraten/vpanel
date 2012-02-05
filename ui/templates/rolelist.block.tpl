<ul class="entrylist">
{foreach from=$roles item=role}
<li class="entry{cycle values="odd,even"}">
{if $showroledel}
 <a style="float:right; margin-top:7px;margin-right:7px;" href="{"roles_del"|___:$role.roleid}" class="delimg" title="Rolle löschen" onClick="return confirm('Rolle wirklich löschen?');">&nbsp;</a>
{/if}
{if $showuserdel}
 <a style="float:right; margin-top:7px;margin-right:7px;" href="{"roles_deluser"|___:$role.roleid:$userid}" class="delimg" title="Benutzer von Rolle löschen" onClick="return confirm('Benutzer von Rolle wirklich löschen?');">&nbsp;</a>
{/if}
<div style="float:left; margin-left:10px;"><a href="{"roles_details"|___:$role.roleid}" class="label">{$role.label}</a><br>
<span class="description">{$role.description}</span></div>
<div style="clear:both;"></div>
</li>
{/foreach}
</ul>
