{include file="header.html.tpl" ansicht="Mitgliederdaten exportieren" menupunkt="mitglied"}
<form action="{"mitglieder_export.export"|___}" method="post">
 <fieldset>
  {if isset($smarty.request.filterid)}
  <input type="hidden" name="filterid" value="{$smarty.request.filterid|escape:html}" />
  {else}
  <table>
  <tr>
   <th>{"Filter:"|__}</th>
   <td>
    <select name="filterid"><option value="">{"(kein Filter)"|__}</option>{foreach from=$filters item=filter}<option value="{$filter.filterid|escape:html}">{$filter.label|escape:html}</option>{/foreach}</select>
   </td>
  </tr>
  </table>
  {/if}


  <table>
  {foreach from=$predefinedfields key=predefinedfieldid item=predefinedfield}
  <tr>
   <td><input type="checkbox" name="simpleexportfields[]" value="{$predefinedfieldid}" checked="checked" /></td>
   <th>{$predefinedfield.label|escape:html}</th>
  </tr>
  {/foreach}
  </table>


  <table>
  <tr class="fields" id="fieldtemplate" style="display:none;">
   <th><input type="text" name="exportfields[]" {literal}onKeyUp="if(this.value!=''){var l=document.getElementsByName('exportfields[]');for (var i=0;i<l.length;i++) {if (i>0 && l[i].value == '') {return;}} $(this.parentNode.parentNode).after($('<tr class=\'fields\'>').append($('#fieldtemplate').html()));}"{/literal} value="" />
   <td><input type="text" name="exportvalues[]" value="" />
   <td><a class="close" href="javascript:void()" onClick="this.parentNode.parentNode.getElementsByTagName('input')[0].value='';this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode)">&times;</a></td>
  </tr>
  <tr class="fields">
   <th><input type="text" name="exportfields[]" value="" {literal}onKeyUp="if(this.value!=''){var l=document.getElementsByName('exportfields[]');for (var i=0;i<l.length;i++) {if (i>0 && l[i].value == '') {return;}} $(this.parentNode.parentNode).after($('<tr class=\'fields\'>').append($('#fieldtemplate').html()));}"{/literal} />
   <td><input type="text" name="exportvalues[]" value="" />
   <td><a class="close" href="javascript:void()" onClick="this.parentNode.parentNode.getElementsByTagName('input')[0].value='';this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode)">&times;</a></td>
  </tr>
  </table>

  <input class="submit" type="submit" name="save" value="{"Weiter"|__}" />
 </fieldset>
</form>
{include file="footer.html.tpl"}
