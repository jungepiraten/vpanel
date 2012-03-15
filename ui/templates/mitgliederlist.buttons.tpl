        <div class="btn-toolbar span6">
		<div class="btn-group">
			<button class="btn" onclick="doNav('{"mitglieder_composefilter"|___}');">{"Filtern"|__}</button>
			<button class="btn dropdown-toggle" data-toggle="dropdown">
				<span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
			{foreach from=$filters item=item_filter}
				<li><a href="{"mitglieder_page"|___:$item_filter.filterid:0}">{$item_filter.label|escape:html}</a></li>
			{/foreach}
			</ul>
		</div>
		{include file="mitgliederfilter.options.tpl" filterid=$filter.filterid}
		<div class="btn-group">
			<a class="btn btn-success dropdown-toggle" data-toggle="dropdown" href="#">
				{"Neues Mitglied anlegen"|__}
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu">
				<li><a href="{"mitglieder_create"|___:-1}">{"(keine Vorlage)"|__}</a></li>
				<li class="divider"></li>
			{foreach from=$mitgliedtemplates item=mitgliedtemplate}
				<li><a href="{"mitglieder_create"|___:$mitgliedtemplate.mitgliedtemplateid}">{$mitgliedtemplate.label|escape:html}</a></li>
			{/foreach}
			</ul>
		</div>
	</div>
	<div class="pagination pagination-right span6">
		<ul>
			{if $page > 1}
			<li><a href="{"mitglieder_page"|___:$filter.filterid:'0'}">&lt;&lt;</a></li>
			{/if}
			{if $page > 0}
			<li><a href="{"mitglieder_page"|___:$filter.filterid:$page-1}">&lt;</a></li>
			{/if}
			{if $page > 3}
				{section name=pages loop=$pagecount start=0 max=2}
				<li><a href="{"mitglieder_page"|___:$filter.filterid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a></li>
				{/section}
				<li class="disabled"><a>...</a></li>
				{section name=pages loop=$pagecount start=$page-1 max=1}
				<li><a href="{"mitglieder_page"|___:$filter.filterid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a></li>
				{/section}
			{else}
				{section name=pages loop=$pagecount start=0 max=$page}
				<li><a href="{"mitglieder_page"|___:$filter.filterid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a></li>
				{/section}
			{/if}
			<li class="active"><a href="{"mitglieder_page"|___:$filter.filterid:$page}">{$page+1}</a></li>
			{if $pagecount-$page > 3}
				{section name=pages loop=$pagecount start=$page+1 max=1}
				<li><a href="{"mitglieder_page"|___:$filter.filterid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a></li>
				{/section}
				<li class="disabled"><a>...</a></li>
				{section name=pages loop=$pagecount start=$pagecount-2 max=2}
				<li><a href="{"mitglieder_page"|___:$filter.filterid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a></li>
				{/section}
			{else}
				{section name=pages loop=$pagecount start=$page+1 max=$pagecount-$page-1}
				<li><a href="{"mitglieder_page"|___:$filter.filterid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a></li>
				{/section}
			{/if}
			{if $page < $pagecount-1}
			<li><a href="{"mitglieder_page"|___:$filter.filterid:$page+1}">&gt;</a></li>
			{/if}
			{if $page < $pagecount-2}
			<li><a href="{"mitglieder_page"|___:$filter.filterid:$pagecount-1}">&gt;&gt;</a></li>
			{/if}
		</ul>
	</div>
