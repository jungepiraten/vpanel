	<div class="btn-toolbar span8">
		<div class="btn-group">
			<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				{$gliederung.label|default:"(alle Gliederungen)"|escape:html}
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu">
				<li><a href="{"dokumente_page"|___:0:$dokumentkategorie.dokumentkategorieid:$dokumentstatus.dokumentstatusid:0}">{"(alle Gliederungen)"}</a></li>
				<li class="divider"></li>
			{foreach from=$gliederungen item=item_gliederung}
				<li><a href="{"dokumente_page"|___:$item_gliederung.gliederungid:$dokumentkategorie.dokumentkategorieid:$dokumentstatus.dokumentstatusid:0}">{$item_gliederung.label|escape:html}</a></li>
			{/foreach}
			</ul>
		</div>
		<div class="btn-group">
			<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				{$dokumentkategorie.label|default:"(alle Kategorien)"|escape:html}
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu">
				<li><a href="{"dokumente_page"|___:$gliederung.gliederungid:0:$dokumentstatus.dokumentstatusid:0}">{"(alle Kategorien)"}</a></li>
				<li class="divider"></li>
			{foreach from=$dokumentkategorien item=item_dokumentkategorie}
				<li><a href="{"dokumente_page"|___:$gliederung.gliederungid:$item_dokumentkategorie.dokumentkategorieid:$dokumentstatus.dokumentstatusid:0}">{$item_dokumentkategorie.label|escape:html}</a></li>
			{/foreach}
			</ul>
		</div>
		<div class="btn-group">
			<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				{$dokumentstatus.label|default:"(alle Zustände)"|escape:html}
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu">
				<li><a href="{"dokumente_page"|___:$gliederung.gliederungid:$dokumentkategorie.dokumentkategorieid:0:0}">{"(alle Zustände)"}</a></li>
				<li class="divider"></li>
			{foreach from=$dokumentstatuslist item=item_dokumentstatus}
				<li><a href="{"dokumente_page"|___:$gliederung.gliederungid:$dokumentkategorie.dokumentkategorieid:$item_dokumentstatus.dokumentstatusid:0}">{$item_dokumentstatus.label|escape:html}</a></li>
			{/foreach}
			</ul>
		</div>
		<div class="btn-group">
			<a class="btn btn-success dropdown-toggle" data-toggle="dropdown" href="#">
				{"Neues Dokument"|__}
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu">
			{foreach from=$dokumenttemplates item=dokumenttemplate}
				<li><a href="{"dokumente_create"|___:$dokumenttemplate.dokumenttemplateid}">{$dokumenttemplate.label|escape:html}</a></li>
			{/foreach}
			</ul>
		</div>
		<div class="btn-group">
			<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				{"Transition ausführen"|__}
				<span class="caret"></span>
			</a>    
			<ul class="dropdown-menu">
				{foreach from=$dokumenttransitionen item=dokumenttransition}
					{if ! $dokumenttransition.hidden}
						<li><a href="{"dokumente_transitionactionmulti"|___:$dokumenttransition.dokumenttransitionid:$gliederung.gliederungid:$dokumentkategorie.dokumentkategorieid:$dokumentstatus.dokumentstatusid}">{$dokumenttransition.label|escape:html}</a></li>
					{/if}
				{/foreach}
			</ul>
		</div>
	</div>
	<div class="pagination pagination-right span4">
		<ul>
			{if $page > 1}
			<li><a href="{"dokumente_page"|___:$gliederung.gliederungid:$dokumentkategorie.dokumentkategorieid:$dokumentstatus.dokumentstatusid:'0'}" class="pagebutton">&lt;&lt;</a></li>
			{/if}
			{if $page > 0}
			<li><a href="{"dokumente_page"|___:$gliederung.gliederungid:$dokumentkategorie.dokumentkategorieid:$dokumentstatus.dokumentstatusid:$page-1}">&lt;</a></li>
			{/if}
			{if $page > 3}
				{section name=pages loop=$pagecount start=0 max=2}
				<li><a href="{"dokumente_page"|___:$gliederung.gliederungid:$dokumentkategorie.dokumentkategorieid:$dokumentstatus.dokumentstatusid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a></li>
				{/section}
				<li class="disabled"><a>...</a></li>
				{section name=pages loop=$pagecount start=$page-1 max=1}
				<li><a href="{"dokumente_page"|___:$gliederung.gliederungid:$dokumentkategorie.dokumentkategorieid:$dokumentstatus.dokumentstatusid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a></li>
				{/section}
			{else}
				{section name=pages loop=$pagecount start=0 max=$page}
				<li><a href="{"dokumente_page"|___:$gliederung.gliederungid:$dokumentkategorie.dokumentkategorieid:$dokumentstatus.dokumentstatusid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a></li>
				{/section}
			{/if}
			<li class="active"><a href="{"dokumente_page"|___:$gliederung.gliederungid:$dokumentkategorie.dokumentkategorieid:$dokumentstatus.dokumentstatusid:$page}">{$page+1}</a></li>
			{if $pagecount-$page > 3}
				{section name=pages loop=$pagecount start=$page+1 max=1}
				<li><a href="{"dokumente_page"|___:$gliederung.gliederungid:$dokumentkategorie.dokumentkategorieid:$dokumentstatus.dokumentstatusid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a></li>
				{/section}
				<li class="disabled"><a>...</a></li>
				{section name=pages loop=$pagecount start=$pagecount-2 max=2}
				<li><a href="{"dokumente_page"|___:$gliederung.gliederungid:$dokumentkategorie.dokumentkategorieid:$dokumentstatus.dokumentstatusid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a></li>
				{/section}
			{else}
				{section name=pages loop=$pagecount start=$page+1 max=$pagecount-$page-1}
				<li><a href="{"dokumente_page"|___:$gliederung.gliederungid:$dokumentkategorie.dokumentkategorieid:$dokumentstatus.dokumentstatusid:$smarty.section.pages.index}">{$smarty.section.pages.index+1}</a></li>
				{/section}
			{/if}
			{if $page < $pagecount-1}
			<li><a href="{"dokumente_page"|___:$gliederung.gliederungid:$dokumentkategorie.dokumentkategorieid:$dokumentstatus.dokumentstatusid:$page+1}">&gt;</a></li>
			{/if}
			{if $page < $pagecount-2}
			<li><a href="{"dokumente_page"|___:$gliederung.gliederungid:$dokumentkategorie.dokumentkategorieid:$dokumentstatus.dokumentstatusid:$pagecount-1}">&gt;&gt;</a></li>
			{/if}
		</ul>
	</div>
