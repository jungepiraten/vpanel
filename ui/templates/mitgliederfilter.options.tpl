<div class="btn-group">
	<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
		{"Aktionen"|__}
		<span class="caret"></span>
	</a>
	<ul class="dropdown-menu">
		{foreach from=$filteractions item=filteraction}
			<li><a href="{"mitglieder_filteraction"|___:$filteraction.actionid:$filterid}" {if $filteraction.permission == "mitglieder_delete"}class="delete"{/if}>{$filteraction.label|__}</a></li>
		{/foreach}
	</ul>
</div>

