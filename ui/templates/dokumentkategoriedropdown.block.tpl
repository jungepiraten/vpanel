<select name="kategorieid">
 <option value="">{$defaulttext}</option>
 {foreach from=$dokumentkategorien item=item_kategorie}
 <option value="{$item_kategorie.dokumentkategorieid|escape:html}" {if $selecteddokumentkategorieid==$item_kategorie.dokumentkategorieid}selected="selected"{/if}>{$item_kategorie.label|escape:html}</option>
 {/foreach}
</select>
