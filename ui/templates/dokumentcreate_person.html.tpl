{capture assign=ansicht}{$dokumenttemplate.label|escape:html} anlegen{/capture}
{include file="header.html.tpl" ansicht=$ansicht menupunkt="dokument"}

<form action="{"dokumente_create"|___:$dokumenttemplate.dokumenttemplateid}" method="post" enctype="multipart/form-data" class="form-horizontal">
 <fieldset>
<div class="control-group">
    <label class="control-label" for="file">{"Datei:"|__}</label>
    <div class="controls">
        <input type="file" name="file" />
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="vorname">{"Name:"|__}</label>
    <div class="controls">
        <input type="text" name="vorname" size="20" /> <input type="text" name="name" size="20" />
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="geburtsdatum">{"Geburtsdatum:"|__}</label>
    <div class="controls">
        <input type="text" name="geburtsdatum" size="20" />
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
