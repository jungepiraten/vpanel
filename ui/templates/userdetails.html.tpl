{include file=header.html.tpl ansicht="Benutzer bearbeiten"}
<div>
<p class="pagetitle">Benutzer &quot;{if isset($user)}{$user.username|escape:html}{/if}&quot; bearbeiten</p>
<div class="mainform">
{include file=userform.block.tpl user=$user}
</div>
<div class="sideinfo">
<span class="sideinfoheader">Rollen:</span>
{include file=rolelist.block.tpl showuserdel=1 userid=$user.userid roles=$userroles}
<form action="{"users_addrole"|___:$user.userid}" method="post" class="useraddrole">
 <fieldset>
  <input type="hidden" name="redirect" value/vpanel/user.php?mode=details&userid=4" />
  <select name="roleid">
   {foreach from=$roles item=role}<option value="{$role.roleid|escape:html}">{$role.label|escape:html}</option>{/foreach}
  </select>
  <input class="submit" type="submit" name="do" value="{"Hinzufuegen"|__}" />
 </fieldset>
</form>
</div>
<div style="clear:both;">&nbsp;</div>
</div>
{include file=footer.html.tpl}
