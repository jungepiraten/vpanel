<select name="{$fieldname|default:gliederungid}">
 <option value="">{$defaulttext}</option>
 {foreach from=$gliederungen item=item_gliederung}
 <option value="{$item_gliederung.gliederungid|escape:html}" {if $selectedgliederungid==$item_gliederung.gliederungid}selected="selected"{/if}>{$item_gliederung.label|escape:html}</option>
 {/foreach}
</select>
