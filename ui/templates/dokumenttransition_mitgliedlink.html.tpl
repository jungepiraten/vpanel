{include file="header.html.tpl" ansicht="Dokument verlinken" menupunkt="dokument"}

<form action="{$link}" method="post" enctype="multipart/form-data" class="form-horizontal" id="mitglieddokumentform">
	<fieldset>
		<div class="control-group">
			<label class="control-label">{"Mitglied:"|__}</label>
			<div class="controls">
				<input type="hidden" id="mitgliedid" name="mitgliedid" />
				{literal}
				<script type="text/javascript">
				function selectMitglied(data) {
					document.getElementById("mitgliedid").value = data.mitgliedid;
					document.getElementById("mitglieddokumentform").submit();
				}
				</script>
				{/literal}
				{include file="mitglieder.suche.block.tpl" mitgliedsuchehandler="selectMitglied"}
			</div>
		</div>
	</fieldset>
</form>
{include file=footer.html.tpl}
