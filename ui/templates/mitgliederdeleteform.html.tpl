{include file="header.html.tpl" ansicht="Mitglied löschen" menupunkt="mitglied"}
<form action="{"mitglieder_filteraction"|___:$action.actionid:$filterid}" method="post" class="form-horizontal">
	<fieldset>
		<div class="control-group">
			<label for="timestamp" class="control-label">Austrittsdatum</label>
			<div class="controls">
				<input type="date" name="timestamp" value="{$smarty.now|date_format:"%Y-%m-%d"}" />
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="kommentar">{"Kommentar:"|__}</label>
			<div class="controls">
				<textarea name="kommentar" cols="10" rows="3"></textarea>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="mailtemplateid">{"Mail versenden:"|__}</label>
			<div class="controls">
				<select>
					<option value="">{"(keine)"|__}</option>
					{foreach from=$mailtemplates item=mt}
						<option value="{$mt.templateid|escape:html}"
						        {if (isset($smarty.request.mailtemplateid) && $smarty.request.mailtemplateid == $mt.templateid) || ($mailtemplate.templateid == $mt.templateid)}selected="selected"{/if}>
							{$mt.label|escape:html}
						</option>
					{/foreach}
				</select>
			</div>
		</div>

		<div class="form-actions">
			<input class="btn btn-danger" type="submit" name="save" value="{"Löschen"|__}" />
		</div>
	</fieldset>
</form>
{include file="footer.html.tpl"}
