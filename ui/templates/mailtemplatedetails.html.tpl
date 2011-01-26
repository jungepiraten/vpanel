{include file="header.html.tpl" ansicht="Mailvorlage bearbeiten"}
<p class="pagetitle">{"Mailvorlage #%d bearbeiten (%s)"|__:$mailtemplate.templateid:$mailtemplate.label}</p>
<div>
<form action="{if isset($mailtemplate)}{"mailtemplates_details"|___:$mailtemplate.templateid}{else}{"mailtemplates_create"|___}{/if}" method="post">
 <fieldset>
 <table>
     <tr>
         <th><label for="label">{"Titel:"|__}</label></th>
         <td><input class="label" type="text" name="label" size="20" value="{if isset($mailtemplate)}{$mailtemplate.label|escape:html}{/if}" /></td>
     </tr>
 </table>
{literal}
<script type="text/javascript">
function f(m) {
	for (var i in m) {
		document.writeln(i);
	}
}
</script>
{/literal}
 <table>
 {assign var=i value=0}
 {foreach from=$mailtemplate.headers key=headerfield item=headervalue}
 {assign var=i value=$i+1}
 <tr class="header">
  <th><input type="text" name="headerfields[{$headerfield|escape:html}]" id="mailheaderfield_{$i}" value="{$headerfield|escape:html}" /></th>
  <td><input type="text" name="headervalues[{$headerfield|escape:html}]" value="{$headervalue|escape:html}" /></td>
  <td>[<a href="javascript:void()" onClick="this.parentNode.parentNode.getElementsByTagName('input')[0].value='';this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode)">x</a>]</td>
 </tr>
 {/foreach}
 <tr class="header" id="mailheader_add">
  <th><input type="text" name="headerfields[]" value="" {literal}onKeyUp="if(this.value!=''){var l=document.getElementsByName('headerfields[]');for (var i=0;i<l.length;i++) {if (l[i].value == '') {return;}} $(this.parentNode.parentNode).after($('<tr class=\'header\'>').append($('#mailheader_add').html()));}"{/literal} />
  <td><input type="text" name="headervalues[]" value="" />
  <td>[<a href="javascript:void()" onClick="this.parentNode.parentNode.getElementsByTagName('input')[0].value='';this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode)">x</a>]</td>
 </tr>
 <tr class="attachments">
  <td colspan="3"></td>
 </tr>
 <tr class="body">
  <td colspan="3"><textarea name="body" rows="10" cols="70">{if isset($mailtemplate)}{$mailtemplate.body|escape:html}{/if}</textarea></td>
 </tr>
 </table>
 <input class="submit" type="submit" name="save" value="{"Speichern"|__}" />
 </fieldset>
</form>
</div>
<div style="clear:both;">&nbsp;</div>
{include file="footer.html.tpl"}
