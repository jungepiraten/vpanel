{capture assign="ansicht"}Benutzer <em>&raquo;{$user.username|escape:html}&laquo;</em> bearbeiten{/capture}
{include file="header.html.tpl" ansicht=$ansicht menupunkt=user}
<div class="row-fluid">
	<div class="span6">
		<h2>{"Benutzer*innendaten"|__}</h2>
		{include file="userform.block.tpl" user=$user}
	</div>
	<div class="span6">
		<h2>Rollen</h2>
		{include file="rolelist.block.tpl" showuserdel=1 userid=$user.userid roles=$userroles}
		<div class="btn-group">
			<a class="btn btn-success dropdown-toggle" data-toggle="dropdown" href="#">
				{"Rolle hinzufügen"|__}
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu">
			{foreach from=$roles item=role}
				<li><a href="{"users_addrole"|___:$user.userid:$role.roleid}">{$role.label|escape:html}</a></li>
			{/foreach}
			</ul>
		</div>
	</div>
</div>
{include file="footer.html.tpl"}
