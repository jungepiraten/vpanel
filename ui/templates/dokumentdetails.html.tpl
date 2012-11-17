{capture assign=ansicht}Dokument <em>&raquo;{$dokument.label|escape:html}&laquo;</em> anzeigen{/capture}
{include file="header.html.tpl" ansicht=$ansicht menupunkt="dokument"}
<div>
<div class="row-fluid">
	<div class="span6">
		{include file="dokumentform.block.tpl" dokument=$dokument dokumentkategorien=$dokumentkategorien dokumentstatuslist=$dokumentstatuslist}
	</div>
	<div class="span6">
		<div class="btn-toolbar">
			<div class="btn-group">
				<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
					{"Transition ausführen"|__}
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu">
					{foreach from=$dokumenttransitionen item=dokumenttransition}
						<li><a href="{"dokumente_transitionaction"|___:$dokumenttransition.dokumenttransitionid:$dokument.dokumentid}">{$dokumenttransition.label|escape:html}</a></li>
					{/foreach}
				</ul>
			</div>
		</div>
		{include file="mitgliederlist.block.tpl" mitglieder=$mitglieder showmitglieddokumentdel=1}

		{foreach from=$dokumentnotizen item=notiz}
		<div class="well">
			<ul style="font-size:0.8em; list-style-type:square;">
				<li class="meta">{"Von %s"|__:$notiz.author.username} {include file="timestamp.tpl" timestamp=$notiz.timestamp}</li>
				{if isset($notiz.nextkategorie)}<li class="nextkategorie">{"Unter %s abgelegt"|__:$notiz.nextkategorie.label}</li>{/if}
				{if isset($notiz.nextstatus)}<li class="nextstatus">{"Als %s markiert"|__:$notiz.nextstatus.label}</li>{/if}
				{if isset($notiz.nextlabel)}<li class="nextlabel">{"In %s umbenannt"|__:$notiz.nextlabel}</li>{/if}
				{if isset($notiz.nextidentifier)}<li class="nextidentifier">{"Als %s abgeheftet"|__:$notiz.nextidentifier}</li>{/if}
				{foreach from=$notiz.addFlags item=flag}<li class="nextaddflag">{$flag.label|escape:html} hinzugef&uuml;gt</li>{/foreach}
				{foreach from=$notiz.delFlags item=flag}<li class="nextdelflag">{$flag.label|escape:html} entfernt</li>{/foreach}
			</ul>
			<div class="kommentar" style="margin-top:0.5em;">{$notiz.kommentar}</div>
		</div>
		{/foreach}
	</div>
</div>
{include file="footer.html.tpl"}
