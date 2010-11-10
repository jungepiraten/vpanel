<ul class="entrylist">
{foreach from=$roles item=role}
<li class="entry{cycle values="odd,even"}">
<div style="float:left; padding-top:7px;"><a href="{"roles_del"|___:$role.roleid}" class="delimg" title="Rolle löschen">&nbsp;</a></div>
<div style="width: 2px; height: 30px; background-color: #8f8f8f; float:left; margin-left:10px;"></div>
<div style="float:left; margin-left:10px;"><a href="{"roles_details"|___:$role.roleid}" class="label">{$role.label}</a><br>
<span class="description">{$role.description}</span></div>
<div style="clear:both;"></div>
</li>
{/foreach}
</ul>
