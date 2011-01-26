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
 <table>
 {foreach from=$mailtemplate.headers key=headerfield item=headervalue}
 <tr class="header">
  <th><input type="text" name="headerfields[{$headerfield|escape:html}]" value="{$headerfield|escape:html}" /></th>
  <td><input type="text" name="headervalues[{$headerfield|escape:html}]" value="{$headervalue|escape:html}" /></td>
 </tr>
 {/foreach}
 <tr class="header">
  <th><input type="text" name="headerfields[]" value="" />
  <td><input type="text" name="headervalues[]" value="" />
 </tr>
 <tr class="attachments">
  <td colspan="2"></td>
 </tr>
 <tr class="body">
  <td colspan="2"><textarea name="body" rows="10" cols="70">{if isset($mailtemplate)}{$mailtemplate.body|escape:html}{/if}</textarea></td>
 </tr>
 </table>
 <input class="submit" type="submit" name="save" value="{"Speichern"|__}" />
 </fieldset>
</form>
</div>
<div style="clear:both;">&nbsp;</div>
{include file="footer.html.tpl"}
