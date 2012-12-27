<form action="{if isset($dokument)}{"dokumente_details"|___:$dokument.dokumentid}{else}{"dokumente_create"|___}{/if}" method="post" class="form-horizontal" enctype="multipart/form-data">
 <fieldset>
<div class="control-group">
    <label class="control-label" for="gliederungid">{"Gliederung:"|__}</label>
    <div class="controls">
        {if isset($revision)}
         {$revision.gliederung.label|escape:html}
        {else}
         {include file=gliederungdropdown.block.tpl defaulttext="(auswählen)" selectedgliederungid=$gliederung.gliederungid}
        {/if}
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="kategorieid">{"Kategorie:"|__}</label>
    <div class="controls">
        {if isset($revision)}
         {include file=dokumentkategoriedropdown.block.tpl defaulttext="(auswählen)" selecteddokumentkategorieid=$revision.kategorie.dokumentkategorieid}
        {else}
         {include file=dokumentkategoriedropdown.block.tpl defaulttext="(auswählen)" selecteddokumentkategorieid=$dokumentkategorie.dokumentkategorieid}
        {/if}
     </div>
</div>
<div class="control-group">
    <label class="control-label" for="statusid">{"Status:"|__}</label>
    <div class="controls">
        {if isset($revision)}
         {include file=dokumentstatusdropdown.block.tpl defaulttext="(auswählen)" selecteddokumentstatusid=$revision.status.dokumentstatusid}
        {else}
         {include file=dokumentstatusdropdown.block.tpl defaulttext="(auswählen)" selecteddokumentstatusid=$dokumentstatus.statusid}
        {/if}
    </div>
</div>
{foreach from=$flags item=flag}
{assign var=flagid value=$flag.flagid}
<div class="control-group">
    <label class="control-label" for="flags[{$flag.flagid}]">{$flag.label|escape:html}</label>
    <div class="controls">
        <input type="checkbox" name="flags[{$flag.flagid}]" {if isset($revision.flags.$flagid) or isset($data.flags.$flagid)}checked="checked"{/if} />
    </div>
</div>
{/foreach}
<div class="control-group">
    <label class="control-label" for="file">{"Datei:"|__}</label>
    <div class="controls">
        {if isset($revision)}
         <a href="{"dokumentrevision_get"|___:$revision.revisionid}" class="btn btn-info">{"Download"|__}</a>
         <button class="btn btn-success" onClick="$(this).parent().empty().append($('<input>').attr('type','file').attr('name','file'))">{"Neu hochladen"|__}</button>
        {else}
         <input type="file" name="file" />
        {/if}
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="identifier">{"Identifikation:"|__}</label>
    <div class="controls">
        <input type="text" name="identifier" value="{if isset($revision)}{$revision.identifier|escape:html}{/if}" />
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="label">{"Titel:"|__}</label>
    <div class="controls">
        <input type="text" name="label" size="40" value="{if isset($revision)}{$revision.label}{/if}" />
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
    {if isset($dokument)}<a class="btn btn-danger delete" href="{"dokumente_del"|___:$dokument.dokumentid}">{"Löschen"|__}</a>{/if}
</div>
 </fieldset>
</form>
