{include file="header.html.tpl" ansicht="Mailvorlagen verwalten" menupunkt="mail"}
<div class="btn-toolbar">
	<div class="btn-group">
		<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
			{$gliederung.label|default:"(alle Gliederungen)"|escape:html}
			<span class="caret"></span>
		</a>
		<ul class="dropdown-menu">
			<li><a href="{"mailtemplates"|___}">{"(alle Gliederungen)"}</a></li>
			<li class="divider"></li>
		{foreach from=$gliederungen item=item_gliederung}
			<li><a href="{"mailtemplates_gliederungid"|___:$item_gliederung.gliederungid}">{$item_gliederung.label|escape:html}</a></li>
		{/foreach}
		</ul>
	</div>
	<div class="btn-group">
		<button class="btn btn-success" onclick="doNav('{"mailtemplates_create_gliederungid"|___:$gliederung.gliederungid}');">
			{"Neue Mailvorlage"|__}
		</button>
	</div>
</div>
<table class="table table-striped table-bordered table-condensed">
	<thead>
		<tr>
			<th>#</th>
			<th>Name</th>
		</tr>
	</thead>
	{foreach from=$mailtemplates item=template}
		<tr onclick="doNav('{"mailtemplates_details"|___:$template.templateid}');">
			<td>{$template.templateid}</td>
			<td>
				<a href="{"mailtemplates_del"|___:$template.templateid}" class="close delete">&times;</a>
				{$template.label|escape:html}
			</td>
		</tr>
	{/foreach}
</table>
<div class="btn-toolbar">
	<div class="btn-group">
		<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
			{$gliederung.label|default:"(alle Gliederungen)"|escape:html}
			<span class="caret"></span>
		</a>
		<ul class="dropdown-menu">
			<li><a href="{"mailtemplates"|___}">{"(alle Gliederungen)"}</a></li>
			<li class="divider"></li>
		{foreach from=$gliederungen item=item_gliederung}
			<li><a href="{"mailtemplates_gliederungid"|___:$item_gliederung.gliederungid}">{$item_gliederung.label|escape:html}</a></li>
		{/foreach}
		</ul>
	</div>
	<div class="btn-group">
		<button class="btn btn-success" onclick="doNav('{"mailtemplates_create_gliederungid"|___:$gliederung.gliederungid}');">
			{"Neue Mailvorlage"|__}
		</button>
	</div>
</div>
{include file="footer.html.tpl"}
