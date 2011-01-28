{include file="header.html.tpl" ansicht="Mail verschicken"}
<p class="pagetitle">Mail verschicken</p>
<p>Hier kommt mal eine Vorschau.</p>
<pre class="header">{foreach from=$mail.headers key=field item=value}{$field|escape:html}: {$value|escape:html}
{/foreach}</pre>
<pre>{$mail.body}</pre>
{include file="footer.html.tpl"}
