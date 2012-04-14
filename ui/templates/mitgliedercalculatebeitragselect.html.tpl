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
		{if isset($beitraglist)}
			<div class="control-group">
				<label class="control-label" for="beitragid">{"Beitrag:"|__}</label>
				<div class="controls">
					<select name="beitragid">
						{foreach from=$beitraglist item=beitrag}
							<option value="{$beitrag.beitragid|escape:html}">{$beitrag.label|escape:html}</option>
						{/foreach}
					</select>
				</div>
			</div>
		{/if}
		{if isset($gliederungen)}
			{foreach from=$gliederungen item=gliederung}
				<div class="control-group">
					<label class="control-label" for="gliederungsAnteil[{$gliederung.gliederungid|escape:html}]">{$gliederung.label|escape:html}</label>
					<div class="controls input-append">
						<input type="text" name="gliederungsAnteil[{$gliederung.gliederungid|escape:html}]" class="span1" />
						<span class="add-on">%</span>
					</div>
				</div>
			{/foreach}
		{/if}
		<div class="form-actions">
			<input class="btn btn-primary" type="submit" name="save" value="{"Start"|__}" />
		</div>
	</fieldset>
</form>
{include file="footer.html.tpl"}
