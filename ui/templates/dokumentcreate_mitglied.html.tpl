{include file="header.html.tpl" ansicht=$title menupunkt="dokument"}

<form action="{$link}" method="post" enctype="multipart/form-data" class="form-horizontal">
	<fieldset>
{if $showupload}
<div class="control-group">
    <label class="control-label" for="file">{"Datei:"|__}</label>
    <div class="controls">
        <input type="file" name="file" />
    </div>
</div>
{/if}
		<div class="control-group">
			<label class="control-label">{"Mitglied:"|__}</label>
			<div class="controls">
				<input type="hidden" id="mitgliedid" name="mitgliedid" />
				{literal}
				<script type="text/javascript">
				function selectMitglied(data) {
					document.getElementById("mitgliedid").value = data.mitgliedid;
					$("#selectMitglied input").val(data.label);
				}
				</script>
				{/literal}
				<div id="selectMitglied">{include file="mitglieder.suche.block.tpl" mitgliedsuchehandler="selectMitglied"}</div>
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
