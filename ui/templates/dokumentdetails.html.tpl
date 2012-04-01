{capture assign=ansicht}Dokument <em>&raquo;{$dokument.label|escape:html}&laquo;</em> anzeigen{/capture}
{include file="header.html.tpl" ansicht=$ansicht menupunkt="dokument"}
<div>
<div class="row-fluid">
	<div class="span6">
		<button class="btn" onclick="$(this).parent().html($('<iframe>').attr('src','{"dokumente_view"|___:$dokument.dokumentid}').attr('width','100%').attr('height','350px'));">Vorschau anzeigen</button>
	</div>
	<div class="span6">
		{include file="dokumentform.block.tpl" dokument=$dokument dokumentkategorien=$dokumentkategorien dokumentstatuslist=$dokumentstatuslist}
	</div>
</div>
<div class="row-fluid">
	<div class="span6">
		<div class="btn-toolbar">
			<div class="btn-group">
				<a href="{"dokumente_mitglied"|___:$dokument.dokumentid}" class="btn">Mitglied verlinken</a>
			</div>
			<div class="btn-group">
				<a class="btn btn-success dropdown-toggle" data-toggle="dropdown" href="#">
					{"Mitglied anlegen"|__}
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu">
				{foreach from=$mitgliedtemplates item=mitgliedtemplate}
					<li><a href="{"dokumente_mitglied_create"|___:$dokument.dokumentid:$mitgliedtemplate.mitgliedtemplateid}">{$mitgliedtemplate.label|escape:html}</a></li>
				{/foreach}
				</ul>
			</div>
		</div>
		{if count($mitglieder) > 0}
		{include file="mitgliederlist.block.tpl" mitglieder=$mitglieder showmitglieddokumentdel=1}
		<div class="btn-toolbar">
			<div class="btn-group">
				<a href="{"dokumente_mitglied"|___:$dokument.dokumentid}" class="btn">Mitglied verlinken</a>
			</div>
			<div class="btn-group">
				<a class="btn btn-success dropdown-toggle" data-toggle="dropdown" href="#">
					{"Mitglied anlegen"|__}
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu">
				{foreach from=$mitgliedtemplates item=mitgliedtemplate}
					<li><a href="{"dokumente_mitglied_create"|___:$dokument.dokumentid:$mitgliedtemplate.mitgliedtemplateid}">{$mitgliedtemplate.label|escape:html}</a></li>
				{/foreach}
				</ul>
			</div>
		</div>
		{/if}
	</div>
	<div class="span6">
		{foreach from=$dokumentnotizen item=notiz}
		<div class="well">
		 <span class="meta">{"Von %s"|__:$notiz.author.username}</span>
		 {if isset($notiz.nextkategorie)}<span class="nextkategorie">{"Unter %s abgelegt"|__:$notiz.nextkategorie.label}</span>{/if}
		 {if isset($notiz.nextstatus)}<span class="nextstatus">{"Als %s markiert"|__:$notiz.nextstatus.label}</span>{/if}
		 <div class="kommentar">{$notiz.kommentar}</div>
		</div>
		{/foreach}
	</div>
</div>
{include file="footer.html.tpl"}
