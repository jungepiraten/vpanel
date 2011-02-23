{include file="header.html.tpl" ansicht="Mail verschicken"}
<p class="pagetitle">{"Mail verschicken"|__}</p>
<a href="{"mitglieder_sendmail.send"|___:$filterid:$mailtemplate.templateid}">{"Los gehts"|__}</a>
<pre class="header">{foreach from=$mail.headers key=field item=value}{$field|escape:html}: {$value|escape:html}
{/foreach}</pre>
<pre>{$mail.body}</pre>
{include file="footer.html.tpl"}
