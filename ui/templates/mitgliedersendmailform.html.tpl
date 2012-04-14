{include file="header.html.tpl" ansicht="Mail verschicken" menupunkt="mitglied"}
<form action="{"mitglieder_filteraction"|___:$action.actionid:$filterid}" method="post" class="form-horizontal">
	<fieldset>
		<div class="control-group">
			<label class="control-label" for="mailtemplateid">{"Mailvorlage:"|__}</label>
			<div class="controls">
				<select name="mailtemplateid">
					{foreach from=$mailtemplates item=mailtemplate}
						<option value="{$mailtemplate.templateid|escape:html}"
						        {if $smarty.request.mailtemplateid == $mailtemplate.templateid}selected="selected"{/if}>
							{$mailtemplate.label|escape:html}
						</option>
					{/foreach}
				</select>
			</div>
		</div>

		<div class="form-actions">
			<input class="btn btn-primary" type="submit" name="save" value="{"Weiter"|__}" />
		</div>
	</fieldset>
</form>
{include file="footer.html.tpl"}
