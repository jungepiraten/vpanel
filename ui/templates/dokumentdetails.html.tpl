{include file="header.html.tpl" ansicht="Dokument anzeigen"}
<p class="pagetitle">Dokument {$dokument.label}</p>
{include file="dokumentform.block.tpl" dokument=$dokument dokumentkategorien=$dokumentkategorien dokumentstatuslist=$dokumentstatuslist}
{foreach from=$dokumentnotizen item=notiz}
<div class="notiz">
 <span class="meta">{"Von %s"|__:$notiz.author.username}</span>
 {if isset($notiz.nextkategorie)}<span class="nextkategorie">{"Unter %s abgelegt"|__:$notiz.nextkategorie.label}</span>{/if}
 {if isset($notiz.nextstatus)}<span class="nextstatus">{"Als %s markiert"|__:$notiz.nextstatus.label}</span>{/if}
 <div class="kommentar">{$notiz.kommentar}</div>
</div>
{/foreach}
{include file="footer.html.tpl"}