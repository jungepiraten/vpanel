{include file="header.html.tpl" ansicht="Mitglied wieder aufnehmen" menupunkt="mitglied"}
<form action="{"mitglieder_filteraction"|___:$action.actionid:$filterid}" method="post" class="form-horizontal">
	<fieldset>
		<div class="control-group">
			<label class="control-label" for="kommentar">{"Kommentar:"|__}</label>
			<div class="controls">
				<textarea name="kommentar" cols="10" rows="3"></textarea>
			</div>
		</div>

		<div class="form-actions">
			<input class="btn btn-success" type="submit" name="save" value="{"Weiter"|__}" />
		</div>
	</fieldset>
</form>
{include file="footer.html.tpl"}
