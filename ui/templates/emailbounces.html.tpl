{include file="header.html.tpl" ansicht="EMail-Bounces"}
<h1 class="pagetitle">EMail-Bounces</h1>
{if isset($smarty.request.mitgliederrevisionid)}<a href="{"mitglieder_details_revision"|___:$smarty.request.mitgliederrevisionid}">Mitglied anzeigen</a>{/if}
{foreach from=$bounces item=bounce}
<p style="text-align:right;">{$bounce.timestamp|date_format:"%d.%m.%Y %H:%M"} <a class="close delete" href="{"emailbounces_delete"|___:$bounce.bounceid}">&times;</a></p>
<pre style="margin:2px; padding:2px; background-color:#cccccc; color:#000000; border:1px solid black; border-radius:4px;">{$bounce.message|escape:html}</pre>
{/foreach}
{include file="footer.html.tpl"}
