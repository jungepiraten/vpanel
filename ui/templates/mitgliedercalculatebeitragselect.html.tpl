{include file="header.html.tpl" ansicht="Beitrag setzen" menupunkt="mitglied"}
<form action="{"mitglieder_filteraction"|___:$action.actionid:$filterid}" method="post" class="form-horizontal">
	<fieldset>
		<input type="hidden" name="redirect" value="{if isset($smarty.post.redirect)}{$smarty.post.redirect|stripslashes|escape:html}{else}{$smarty.server.HTTP_REFERER|escape:html}{/if}" />
		<div class="control-group">
			<label class="control-label" for="timestamp">{"Buchungen nach:"|__}</label>
			<div class="controls">
				<input type="text" name="starttimestamp" value="{$smarty.now-30*24*60*60|date_format:"%d.%m.%Y"}" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="timestamp">{"Buchungen vor:"|__}</label>
			<div class="controls">
				<input type="text" name="endtimestamp" value="{$smarty.now|date_format:"%d.%m.%Y"}" />
			</div>
		</div>
		{if isset($userlist)}
			<div class="control-group">
				<label class="control-label" for="userid">{"Benutzer*in:"|__}</label>
				<div class="controls">
					<select name="userid">
						{foreach from=$userlist item=user}
							<option value="{$user.userid|escape:html}">{$user.username|escape:html}</option>
						{/foreach}
					</select>
				</div>
			</div>
		{/if}
		{if isset($beitraglist)}
			<div class="control-group">
				<label class="control-label" for="beitragid[]">{"Beitrag:"|__}</label>
				<div class="controls">
					<select name="beitragid[]" multiple="multiple" size="5">
						{foreach from=$beitraglist item=beitrag}
							<option value="{$beitrag.beitragid|escape:html}">{$beitrag.label|escape:html}</option>
						{/foreach}
					</select>
				</div>
			</div>
		{/if}
		{if isset($gliederungen)}
			<table class="table table-striped table-bordered">
				<tr>
					<th>&nbsp;</th>
					{foreach from=$gliederungen item=gliederung}
						<th>Beitrag für {$gliederung.label|escape:html}</th>
					{/foreach}
				</tr>
				{foreach from=$gliederungen item=gliederung}
					<tr>
						<th>Mitglied bei {$gliederung.label|escape:html}</th>
						{foreach from=$gliederungen item=g2}
							<td>
								<div class="input-append">
									<input type="text" name="gliederungsAnteil[{$gliederung.gliederungid|escape:html}][{$g2.gliederungid|escape:html}]" style="width:2em" value="0" />
									<span class="add-on">%</span>
								</div>
							</td>
						{/foreach}
					</tr>
				{/foreach}
			</table>
		{/if}
		<div class="form-actions">
			<input class="btn btn-primary" type="submit" name="save" value="{"Start"|__}" />
		</div>
	</fieldset>
</form>
{include file="footer.html.tpl"}
