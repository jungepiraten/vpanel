{include file="header.html.tpl" ansicht="Mitgliederdaten exportieren" menupunkt="mitglied"}
<form action="{"mitglieder_filteraction"|___:$action.actionid:$filterid}" method="post" class="form-horizontal">
	<fieldset>
		<input type="hidden" name="export" value="export" />

		<div class="control-group" id="fieldtemplate" style="display:none;">
			<div class="controls">
				<input type="text" name="exportfields[]" value=""
				       {literal}onKeyUp="if(this.value!=''){var l=document.getElementsByName('exportfields[]');for (var i=0;i<l.length;i++) {if (i>0 && l[i].value == '') {return;}} $(this.parentNode.parentNode).after($('<tr class=\'fields\'>').append($('#fieldtemplate').html()));}"{/literal} />
				<input type="text" name="exportvalues[]" value="" />
				<a class="close" href="javascript:void()" onClick="this.parentNode.parentNode.getElementsByTagName('input')[0].value='';this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode)">&times;</a></td>
			</div>
		</div>
		<div id="fields">
		</div>

<script type="text/javascript">

{literal}
function addControlGroup(field, value) {
	var text_field = $("<input>").attr("type","text").attr("name","exportfields[]").val(field).keyup(checkControlFields);
	var text_value = $("<input>").attr("type","text").attr("name","exportvalues[]").val(value).keyup(checkControlFields);
	var close_link = $("<a>").addClass("close").html("&times;").click(function () {
		text_field.val("");
		text_value.val("");
		container.remove();
	});
	var container = $("<div>").append(text_field).append(text_value).append(close_link);

	$("#fields").append(container);
}

function checkControlFields() {
	var fields = $("#fields div").get();
	for (var i=0; i<fields.length; i++) {
		var field = $(fields[i]);
		if (i>0 && field.find("input[name='exportfields[]']").val() == "") {
			return;
		}
	}
	addControlGroup("","");
}
{/literal}

{foreach from=$predefinedfields key=predefinedfieldid item=predefinedfield}
addControlGroup("{$predefinedfield.label|escape:html}", "{$predefinedfield.template|escape:html}");
{/foreach}
addControlGroup("","");

</script>

		<div class="form-action">
			<input class="btn btn-primary" type="submit" name="save" value="{"Weiter"|__}" />
		</div>
	</fieldset>
</form>
{include file="footer.html.tpl"}
