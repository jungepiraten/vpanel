{include file="header.html.tpl" ansicht="Dokument weiterleiten" menupunkt="dokument"}

<form action="{$link}" method="post" enctype="multipart/form-data" class="form-horizontal">
	<fieldset>
		<div class="control-group">
			<label class="control-label" for="email">{"E-Mail:"|__}</label>
			<div class="controls">
				<input type="text" name="email" />
			</div>
		</div>
		<div class="form-actions">
			<button class="btn btn-primary submit" type="submit" name="forward" value="1">{"Weiterleiten"|__}</button>
		</div>
	</fieldset>
</form>

{include file="footer.html.tpl"}
