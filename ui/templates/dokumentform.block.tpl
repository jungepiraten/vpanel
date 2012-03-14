<form action="{if isset($dokument)}{"dokumente_details"|___:$dokument.dokumentid}{else}{"dokumente_create"|___}{/if}" method="post" class="form-horizontal" enctype="multipart/form-data">
 <fieldset>
<div class="control-group">
    <label class="control-label" for="gliederungid">{"Gliederung:"|__}</label>
    <div class="controls">
        {if isset($dokument)}
         {$dokument.gliederung.label|escape:html}
        {else}
         {include file=gliederungdropdown.block.tpl defaulttext="(auswählen)" selectedgliederungid=$gliederung.gliederungid}
        {/if}
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="kategorieid">{"Kategorie:"|__}</label>
    <div class="controls">
        {if isset($dokument)}
         {include file=dokumentkategoriedropdown.block.tpl defaulttext="(auswählen)" selecteddokumentkategorieid=$dokument.dokumentkategorie.dokumentkategorieid}
        {else}
         {include file=dokumentkategoriedropdown.block.tpl defaulttext="(auswählen)" selecteddokumentkategorieid=$dokumentkategorie.dokumentkategorieid}
        {/if}
     </div>
</div>
<div class="control-group">
    <label class="control-label" for="statusid">{"Status:"|__}</label>
    <div class="controls">
        {if isset($dokument)}
         {include file=dokumentstatusdropdown.block.tpl defaulttext="(auswählen)" selecteddokumentstatusid=$dokument.dokumentstatus.dokumentstatusid}
        {else}
         {include file=dokumentstatusdropdown.block.tpl defaulttext="(auswählen)" selecteddokumentstatusid=$dokumentstatus.dokumentstatusid}
        {/if}
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="file">{"Datei:"|__}</label>
    <div class="controls">
        {if isset($dokument)}
         <a href="{"dokumente_get"|___:$dokument.dokumentid}">{"Download"|__}</a>
        {else}
         <input type="file" name="file" />
        {/if}
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="idkey">{"Identifikation:"|__}</label>
    <div class="controls">
        {if isset($dokument)}
         {$dokument.identifier|escape:html}
        {else}
         <input type="text" name="idkey" />
        {/if}
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="label">{"Titel:"|__}</label>
    <div class="controls">
        <input type="text" name="label" size="40" value="{if isset($dokument)}{$dokument.label}{/if}" />
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="kommentar">{"Kommentar:"|__}</label>
    <div class="controls">
        <textarea name="kommentar" cols="10" rows="3"></textarea>
    </div>
</div>
<div class="form-actions">
    <button class="btn btn-primary submit" type="submit" name="save" value="1">{"Speichern"|__}</button>
    <button class="btn">{"Abbrechen"|__}</button>
</div>
 </fieldset>
</form>
