{capture assign=ansicht}Dokument <em>&raquo;{$dokument.latest.label|escape:html}&laquo;</em> anzeigen{/capture}
{include file="header.html.tpl" ansicht=$ansicht menupunkt="dokument"}

{include file="dokumentbadges.block.tpl" badges=$dokument.badges}

<div class="row-fluid">
	<div class="span6">
		{include file="dokumentform.block.tpl" dokument=$dokument revision=$dokument.latest dokumentkategorien=$dokumentkategorien dokumentstatuslist=$dokumentstatuslist}
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
						<li><a href="{"dokumente_transitionaction"|___:$dokumenttransition.dokumenttransitionid:$dokument.filterid}">{$dokumenttransition.label|escape:html}</a></li>
					{/foreach}
				</ul>
			</div>
		</div>
		{include file="mitgliederlist.block.tpl" mitglieder=$mitglieder showmitglieddokumentdel=1}

		{foreach from=$dokumentrevisionen item=revision}
		<div class="well">
			<ul style="font-size:0.8em; list-style-type:square;">
				<li class="meta">{"Von %s"|__:$revision.user.username} {include file="timestamp.tpl" timestamp=$revision.timestamp}</li>
				{if !isset($revisionkategorie) || $revisionkategorie != $revision.kategorie.dokumentkategorieid}{assign var=revisionkategorie value=$revision.kategorie.dokumentkategorieid}
					<li class="nextkategorie">{"Unter %s abgelegt"|__:$revision.kategorie.label}</li>{/if}
				{if !isset($revisionstatus) || $revisionstatus != $revision.status.dokumentstatusid}{assign var=revisionstatus value=$revision.status.dokumentstatusid}
					<li class="nextstatus">{"Als %s markiert"|__:$revision.status.label}</li>{/if}
				{if !isset($revisionfile) || $revisionfile != $revision.file.fileid}{assign var=revisionfile value=$revision.file.fileid}
					<li class="nextfile"><a href="{"dokumentrevision_get"|___:$revision.revisionid}">{"Neue Datei hochgeladen"|__}</a></li>{/if}
				{if !isset($revisionidentifier) || $revisionidentifier != $revision.identifier}{assign var=revisionidentifier value=$revision.identifier}
					<li class="nextidentifier">{"Als %s abgeheftet"|__:$revision.identifier}</li>{/if}
				{if !isset($revisionlabel) || $revisionlabel != $revision.label}{assign var=revisionlabel value=$revision.label}
					<li class="nextlabel">{"In %s umbenannt"|__:$revision.label}</li>{/if}
				{foreach from=$revision.flags key=flagid item=flag}{if !isset($revisionflags) || $revision.flags.$flagid != $revisionflags.$flagid}
					<li class="nextaddflag">{$flag.label|escape:html} hinzugef&uuml;gt</li>{/if}{/foreach}
				{if isset($revisionflags)}{foreach from=$revisionflags key=flagid item=flag}{if $revision.flags.$flagid != $revisionflags.$flagid}
					<li class="nextdelflag">{$flag.label|escape:html} entfernt</li>{/if}{/foreach}{/if}
				{assign var=revisionflags value=$revision.flags}
			</ul>
			<div class="kommentar" style="margin-top:0.5em;">{$revision.kommentar}</div>
		</div>
		{/foreach}
	</div>
</div>
{include file="footer.html.tpl"}
