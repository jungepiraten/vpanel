{include file="header.html.tpl" ansicht="Dokument einsortieren" menupunkt="dokument"}

<form action="{$link}" method="post" enctype="multipart/form-data" class="form-horizontal">
	<fieldset>
		<div class="control-group">
			<label class="control-label" for="label">{"Vorlage:"|__}</label>
			<div class="controls">
				<select name="templateid">{foreach from=$dokumenttemplates item=template}<option value="{$template.dokumenttemplateid|escape:html}">{$template.label|escape:html}</option>{/foreach}</select>
			</div>
		</div>
		<div class="form-actions">
			<button class="btn btn-primary submit" type="submit" name="continue" value="1">{"Weiter"|__}</button>
		</div>
	</fieldset>
</form>

{include file="footer.html.tpl"}
