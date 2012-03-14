{include file="header.html.tpl" ansicht="Anhang hochladen" menupunkt="mail"}
<form action="{"mailtemplateattachment_create"|___:$mailtemplate.templateid}" method="post" enctype="multipart/form-data" class="form-horizontal">
 <fieldset>
  <input type="hidden" name="redirect" value="{if isset($smarty.post.redirect)}{$smarty.post.redirect|stripslashes|escape:html}{else}{$smarty.server.HTTP_REFERER|escape:html}{/if}" />

<div class="control-group">
    <label class="control-label" for="file">{"Datei:"|__}</label>
    <div class="controls">
        <input type="file" name="attachment" />
    </div>
</div>

<div class="form-actions">
    <button class="btn btn-primary submit" type="submit" name="save" value="1">{"Speichern"|__}</button>
    <button class="btn">{"Abbrechen"|__}</button>
</div>
 </fieldset>
</form>
{include file="footer.html.tpl"}

