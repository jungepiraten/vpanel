{include file="header.html.tpl" ansicht="Dokument anzeigen"}
<p class="pagetitle">Dokument {$dokument.label}</p>
<iframe src="{"dokumente_view"|___:$dokument.dokumentid}" width="650px" height="300px"></iframe>
{include file="dokumentform.block.tpl" dokument=$dokument dokumentkategorien=$dokumentkategorien dokumentstatuslist=$dokumentstatuslist}
{include file="mitgliederlist.block.tpl" mitglieder=$mitglieder}
<a href="{"dokumente_mitglied"|___:$dokument.dokumentid}">Mitglied verlinken</a>
<a href="{"dokumente_mitglied_create"|___:$dokument.dokumentid}">Mitglied anlegen</a>
{foreach from=$dokumentnotizen item=notiz}
<div class="notiz">
 <span class="meta">{"Von %s"|__:$notiz.author.username}</span>
 {if isset($notiz.nextkategorie)}<span class="nextkategorie">{"Unter %s abgelegt"|__:$notiz.nextkategorie.label}</span>{/if}
 {if isset($notiz.nextstatus)}<span class="nextstatus">{"Als %s markiert"|__:$notiz.nextstatus.label}</span>{/if}
 <div class="kommentar">{$notiz.kommentar}</div>
</div>
{/foreach}
{include file="footer.html.tpl"}
