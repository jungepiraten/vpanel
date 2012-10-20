{include file="header.html.tpl" ansicht="Mail verschicken" menupunkt="mitglied"}

{foreach from=$mailtemplates item=mt}
<div style="display:none;" id="mailbody-{$mt.templateid}">{$mt.body|escape:html}</div>
{/foreach}

<script type="text/javascript">
<!--

{literal}
function changeMailTemplateView(id) {
	mailClearHeader();
	for (var field in headers[id]) {
		mailAddHeader(field, headers[id][field]);
	}
	mailSetBody($("#mailbody-" + id).html());
}
{/literal}

var headers = {literal}{}{/literal};
{foreach from=$mailtemplates item=mt}
headers[{$mt.templateid}] = {literal}{}{/literal};
{foreach from=$mt.headers key=headerfield item=headervalue}headers[{$mt.templateid}]["{$headerfield}"] = "{$headervalue}";{/foreach}
{/foreach}

-->
</script>

<div class="form-horizontal">
	<div class="control-group">
		<label class="control-label" for="mailtemplateid">{"Mailvorlage:"|__}</label>
		<div class="controls">
			<select onChange="changeMailTemplateView(this.value);">
				<option value="">{"(keine)"|__}</option>
				{foreach from=$mailtemplates item=mt}
					<option value="{$mt.templateid|escape:html}"
					        {if $smarty.request.mailtemplateid == $mt.templateid}selected="selected"{/if}>
						{$mt.label|escape:html}
					</option>
				{/foreach}
			</select>
		</div>
	</div>
</div>

<form action="{"mitglieder_filteraction"|___:$action.actionid:$filterid}" method="post" class="form-horizontal mailtemplate-">
	<fieldset>
		{if $mailtemplate != null}
			{include file="mailform.block.html" headers=$mailtemplate.headers attachments=$mailtemplate.attachments body=$mailtemplate.body}
		{else}
			{include file="mailform.block.html"}
		{/if}
		<div class="form-actions">
			<button class="btn btn-primary submit" type="submit" name="save" value="1">{"Weiter"|__}</button>
		</div>
	</fieldset>
</form>

{include file="footer.html.tpl"}
