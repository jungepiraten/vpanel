<form action="{if isset($beitrag)}{"beitraege_details"|___:$beitrag.beitragid}{else}{"beitraege_create"|___}{/if}" method="post" class="form form-horizontal">
 <fieldset>
    <div class="control-group">
        <label class="control-label" for="label">{"Titel:"|__}</label>
        <div class="controls">
            <input type="text" name="label" value="{if isset($beitrag)}{$beitrag.label|escape:html}{/if}" />
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="hoehe">{"Beitrag:"|__}</label>
        <div class="controls">
            <div class="input-append">    
                <input type="text" name="hoehe" value="{if isset($beitrag)}{$beitrag.hoehe|string_format:"%.2f"}{/if}" class="input-small"/><span class="add-on">&euro;</span>
            </div>
        </div>
    </div>
    <div class="form-actions">
        <Button class="btn btn-primary" type="submit" name="save" value="1">{"Speichern"|__}</button>
        <button class="btn">{"Abbrechen"|__}</button>
    </div>
 </fieldset>
</form>

