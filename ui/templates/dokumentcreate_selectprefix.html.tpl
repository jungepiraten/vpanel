{include file="header.html.tpl" ansicht=$title menupunkt="dokument"}

<form action="{$link}" method="post" enctype="multipart/form-data" class="form-horizontal">
 <fieldset>
<div class="control-group">
    <label class="control-label" for="option">{"Auswahl:"|__}</label>
    <div class="controls">
        <select name="option">{foreach from=$options key=optionid item=option}<option value="{$optionid|escape:html}">{$option|escape:html}</option>{/foreach}</select>
    </div>
</div>
{if isset($datefield)}
<div class="control-group">
    <label class="control-label" for="timestamp">{$datefield|escape:html}:</label>
    <div class="controls">
        <input type="date" name="timestamp" value="{$smarty.now|date_format:"%Y-%m-%d"}" />
    </div>
</div>
{/if}
{if $showupload}
<div class="control-group">
    <label class="control-label" for="file">{"Datei:"|__}</label>
    <div class="controls">
        <input type="file" name="file" />
    </div>
</div>
{/if}
<div class="control-group">
    <label class="control-label" for="label">{"Titel:"|__}</label>
    <div class="controls">
        <input type="text" name="label" size="40" />
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="kommentar">{"Kommentar:"|__}</label>
    <div class="controls">
        <textarea name="kommentar" cols="40" rows="10"></textarea>
    </div>
</div>
<div class="form-actions">
    <button class="btn btn-primary submit" type="submit" name="save" value="1">{"Speichern"|__}</button>
    <button class="btn">{"Abbrechen"|__}</button>
</div>
 </fieldset>
</form>

{include file="footer.html.tpl"}
