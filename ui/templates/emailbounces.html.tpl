{include file="header.html.tpl" ansicht="EMail-Bounces"}
<h1 class="pagetitle">EMail-Bounces</h1>
{foreach from=$bounces item=bounce}
<pre style="margin:2px; padding:2px; background-color:#cccccc; color:#000000; border:1px solid black; border-radius:4px;">{$bounce.message|escape:html}</pre>
{/foreach}
{include file="footer.html.tpl"}
