<form class="form-horizontal" action="{if isset($role)}{"roles_details"|___:$role.roleid}{else}{"roles_create"|___}{/if}" method="post">
 <fieldset>
    <div class="control-group">
        <label class="control-label" for="label">{"Bezeichnung:"|__}</label>
        <div class="controls">
            <input type="text" name="label" value="{if isset($role)}{$role.label|escape:html}{/if}" />
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="description">{"Beschreibung:"|__}</label>
        <div class="controls">
            
            <textarea name="description" cols="50" rows="6">{if isset($role)}{$role.description|escape:html}{/if}</textarea>
        </div>
    </div>
<div class="form-actions">
    <button class="btn btn-primary" type="submit" name="save" value="1">{"Speichern"|__}</button>
    <button class="btn">{"Abbrechen"|__}</button>
</div>
 </fieldset>
</form>
