{if $pagecount > 1}
	<div class="pagination">
		<ul>
			{if $page > 1}
				<li><a href="{"beitraege_details_page"|___:$beitrag.beitragid:'0'}" class="pagebutton">&lt;&lt;</a></li>
			{/if}
			{if $page > 0}
				<li><a href="{"beitraege_details_page"|___:$beitrag.beitragid:$page-1}">&lt;</a></li>
			{/if}
			{if $page > 3}
				{section name=pages loop=$pagecount start=0 max=2}
					<li><a href="{"beitraege_details_page"|___:$beitrag.beitragid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a></li>
				{/section}
				<li class="disabled"><a>...</a></span>
				{section name=pages loop=$pagecount start=$page-1 max=1}
					<li><a href="{"beitraege_details_page"|___:$beitrag.beitragid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a></li>
				{/section}
			{else}
				{section name=pages loop=$pagecount start=0 max=$page}
					<li><a href="{"beitraege_details_page"|___:$beitrag.beitragid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a></li>
				{/section}
			{/if}
			<li class="active"><a href="{"beitraege_details_page"|___:$beitrag.beitragid:$page}"class="curpage">{$page+1}</a></li>
			{if $pagecount-$page > 3}
				{section name=pages loop=$pagecount start=$page+1 max=1}
					<li><a href="{"beitraege_details_page"|___:$beitrag.beitragid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a></li>
				{/section}
				<li class="disabled"><a>...</a></span>
				{section name=pages loop=$pagecount start=$pagecount-2 max=2}
					<li><a href="{"beitraege_details_page"|___:$beitrag.beitragid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a></li>
				{/section}
			{else}
				{section name=pages loop=$pagecount start=$page+1 max=$pagecount-$page-1}
					<li><a href="{"beitraege_details_page"|___:$beitrag.beitragid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a></li>
				{/section}
			{/if}
			{if $page < $pagecount-1}
				<li><a href="{"beitraege_details_page"|___:$beitrag.beitragid:$page+1}">&gt;</a></li>
			{/if}
			{if $page < $pagecount-2}
				<li><a href="{"beitraege_details_page"|___:$beitrag.beitragid:$pagecount-1}">&gt;&gt;</a></li>
			{/if}
		</ul>
	</div>
{/if}
