{include file="header.html.tpl" ansicht="Mitglied anlegen" menupunkt="dokument"}

<form action="{$link}" method="post" class="form-horizontal">
	<fieldset>
		<div class="control-group">
			<label class="control-label" for="label">{"Vorlage:"|__}</label>
			<div class="controls">
				<select name="mitgliedtemplateid">{foreach from=$mitgliedtemplates item=template}<option value="{$template.mitgliedtemplateid|escape:html}">{$template.label|escape:html}</option>{/foreach}</select>
			</div>
		</div>
		<div class="form-actions">
			<button class="btn btn-primary submit" type="submit" name="continue" value="1">{"Weiter"|__}</button>
			<button class="btn">{"Abbrechen"|__}</button>
		</div>
	</fieldset>
</form>

{include file="footer.html.tpl"}
