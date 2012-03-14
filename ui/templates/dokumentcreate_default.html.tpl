{capture assign="ansicht"}{$dokumenttemplate.label} anlegen{/capture}
{include file="header.html.tpl" ansicht=$ansicht menupunkt="dokument"}

<form action="{if isset($user)}{"users_details"|___:$user.userid}{else}{"users_create"|___}{/if}" method="post" class="form-horizontal">
 <fieldset>
<div class="control-group">
    <label class="control-label" for="username">{"Username:"|__}</label>
    <div class="controls">
        <input class="input" type="text" name="username" value="{if isset($user)}{$user.username|escape:html}{/if}" />
    </div>
</div>

<form action="{"dokumente_create"|___}" method="post" class="filter" enctype="multipart/form-data" class="form-horizontal">
 <fieldset>
  <input type="hidden" name="dokumenttemplateid" value="{$dokumenttemplate.dokumenttemplateid|escape:html}" />
<div class="control-group">
    <label class="control-label" for="file">{"Datei:"|__}</label>
    <div class="controls">
        <input type="file" name="file" />
    </div>
</div>
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
