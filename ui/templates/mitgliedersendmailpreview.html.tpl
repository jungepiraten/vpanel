{include file="header.html.tpl" ansicht="Mail verschicken" menupunkt="mitglied"}
<form action="{"mitglieder_filteraction"|___:$action.actionid:$filterid}" method="post">
	<fieldset>
		<input type="hidden" name="mailtemplatecode" value="{$mailtemplatecode|escape:html}" />
		<button class="btn" name="form" value="1">{"Zurück"|__}</button>
		<button class="btn btn-primary" name="sendmail" value="send">{"Los gehts"|__}</button>
	</fieldset>
</form>
<pre style="margin:2px; padding:2px; background-color:#cccccc; color:#000000; border:1px solid black; border-radius:4px;">{foreach from=$mail.headers key=field item=value}{$field|escape:html}: {$value|escape:html}
{/foreach}</pre>
<pre>{$mail.body|escape:html}</pre>
{include file="footer.html.tpl"}
