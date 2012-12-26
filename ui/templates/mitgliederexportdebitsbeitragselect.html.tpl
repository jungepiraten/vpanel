{include file="header.html.tpl" ansicht="Beitrag setzen" menupunkt="mitglied"}
<form action="{"mitglieder_filteraction"|___:$action.actionid:$filterid}" method="post" class="form-horizontal">
	<fieldset>
		<input type="hidden" name="redirect" value="{if isset($smarty.post.redirect)}{$smarty.post.redirect|stripslashes|escape:html}{else}{$smarty.server.HTTP_REFERER|escape:html}{/if}" />
		<div class="control-group">
			<label class="control-label" for="beitragid">{"Beitrag:"|__}</label>
			<div class="controls">
				<select name="beitragid">
					<option value="-">Alle</option>
					{foreach from=$beitraglist item=beitrag}
						<option value="{$beitrag.beitragid|escape:html}">{$beitrag.label|escape:html}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="form-actions">
			<input class="btn btn-primary" type="submit" name="save" value="{"Start"|__}" />
		</div>
	</fieldset>
</form>
{include file="footer.html.tpl"}
