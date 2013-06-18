{include file="header.html.tpl" ansicht=$title menupunkt="dokument"}

<form action="{$link}" method="post" enctype="multipart/form-data" class="form-horizontal">
 <fieldset>
<div class="control-group">
    <label class="control-label" for="file">{"Datei:"|__}</label>
    <div class="controls">
        <input type="file" name="file" />
    </div>
</div>
<div class="form-actions">
    <button class="btn btn-primary submit" type="submit" name="save" value="1">{"Speichern"|__}</button>
    <button class="btn">{"Abbrechen"|__}</button>
</div>
 </fieldset>
</form>

{include file="footer.html.tpl"}
