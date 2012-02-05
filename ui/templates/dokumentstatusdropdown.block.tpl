<select name="{$fieldname|default:statusid}">
 <option value="">{$defaulttext}</option>
 {foreach from=$dokumentstatuslist item=item_status}
  <option value="{$item_status.dokumentstatusid|escape:html}" {if $selecteddokumentstatusid==$item_status.dokumentstatusid}selected="selected"{/if}>{$item_status.label|escape:html}</option>
 {/foreach}
</select>
