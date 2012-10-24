{include file="header.html.tpl" ansicht="Mitglied löschen" menupunkt="mitglied"}
<form action="{"mitglieder_filteraction"|___:$action.actionid:$filterid}" method="post" class="form-horizontal">
	<fieldset>
		<div class="control-group">
			<label for="timestamp" class="control-label">Austrittsdatum</label>
			<div class="controls">
				<input type="text" name="timestamp" value="{$smarty.now|date_format:"%d.%m.%Y"}" />
			</div>
		</div>

		<div class="form-action">
			<input class="btn btn-danger" type="submit" name="save" value="{"Löschen"|__}" />
		</div>
	</fieldset>
</form>
{include file="footer.html.tpl"}
