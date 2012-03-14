{include file="header.html.tpl" ansicht="Mail verschicken" menupunkt="mitglied"}
<a href="{"mitglieder_sendmail.send"|___:$filterid:$mailtemplate.templateid}">{"Los gehts"|__}</a>
<pre style="margin:2px; padding:2px; background-color:#cccccc; color:#000000; border:1px solid black; border-radius:4px;">{foreach from=$mail.headers key=field item=value}{$field|escape:html}: {$value|escape:html}
{/foreach}</pre>
<pre>{$mail.body}</pre>
{include file="footer.html.tpl"}
