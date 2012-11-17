{capture assign="ansicht"}Mitglied #{$mitglied.mitgliedid} ({if isset($mitglied.latest.natperson)}{$mitglied.latest.natperson.vorname|escape:html} {$mitglied.latest.natperson.name|escape:html}{/if}{if isset($mitglied.latest.jurperson)}{$mitglied.latest.jurperson.label|escape:html}{/if}) bearbeiten{/capture}
{include file="header.html.tpl" ansicht=$ansicht menupunkt="mitglied"}

<div class="tabbable">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#mitgliederdetails-kartei" data-toggle="tab">{"Kartei"|__}</a></li>
		<li><a href="#mitgliederdetails-notizen" data-toggle="tab">{"Notizen"|__}</a></li>
		<li><a href="#mitgliederdetails-dokumente" data-toggle="tab">{"Dokumente"|__}</a></li>
		<li><a href="#mitgliederdetails-beitraege" data-toggle="tab">{"Beiträge"|__}</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="mitgliederdetails-kartei">
			<div class="btn-toolbar">
				<div class="btn-group">
					<a class="btn btn-info dropdown-toggle" data-toggle="dropdown" href="#">
						Version vom {$mitgliedrevision.timestamp|date_format:"%d.%m.%Y um %H:%M Uhr"} {if isset($mitgliedrevision.user)}von {$mitgliedrevision.user.username|escape:html}{/if}
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						{foreach from=$mitgliedrevisions item=rev}
							<li><a href="{"mitglieder_details_revision"|___:$rev.revisionid}">Version vom {$rev.timestamp|date_format:"%d.%m.%Y um %H:%M Uhr"} {if isset($rev.user)}von {$rev.user.username|escape:html}{/if}</a></li>
						{/foreach}
					</ul>
				</div>
				{include file="mitgliederfilter.options.tpl" filterid=$mitglied.filterid}
			</div>

			{include file="mitgliederform.block.tpl" mitglied=$mitglied}
		</div>
		<div class="tab-pane" id="mitgliederdetails-notizen">
			<div class="btn-toolbar">
				<div class="btn-group">
					<a data-toggle="modal" href="#notiz-add" class="btn btn-primary">Notiz hinzufügen</a>
				</div>
			</div>
			<form action="{"mitglieder_details"|___:$mitglied.mitgliedid}" method="post" class="form-horizontal modal" style="display:none;" id="notiz-add">
				<div class="modal-header">
					<a class="close" data-dismiss="modal">×</a>
					<h3>Notiz hinzufügen</h3>
				</div>
				<fieldset class="modal-body">
					<div class="control-group">
						<label class="control-label" for="kommentar">{"Notiz:"|__}</label>
						<div class="controls">
							<textarea cols="35" rows="2" name="kommentar"></textarea>
						</div>
					</div>
				</fieldset>
				<div class="modal-footer">
					<button class="btn btn-success submit" type="submit" name="addnotiz" value="1">{"Hinzufügen"|__}</button>
				</div>
			</form>
			{foreach from=$mitgliednotizen item=notiz}
				<div class="well">
					<span class="meta">{if isset($notiz.author)}{"Von %s"|__:$notiz.author.username}{/if}</span>
					{include file="timestamp.tpl" timestamp=$notiz.timestamp}
					<div class="kommentar">{$notiz.kommentar}</div>
				</div>
			{/foreach}
		</div>
		<div class="tab-pane" id="mitgliederdetails-dokumente">
			<div class="btn-toolbar">
				<div class="btn-group">
					<a href="{"mitglieder_dokument"|___:$mitglied.mitgliedid}" class="btn btn-primary">Dokument verlinken</a>
				</div>
			</div>
			{if count($dokumente) > 0}
				{include file=dokumentlist.block.tpl dokumente=$dokumente showmitglieddokumentdel=1}
				{if count($dokumente) > 5}
					<div class="btn-toolbar">
						<div class="btn-group">
							<a href="{"mitglieder_dokument"|___:$mitglied.mitgliedid}" class="btn">Dokument verlinken</a>
						</div>
					</div>
				{/if}
			{/if}
		</div>

		<div class="tab-pane" id="mitgliederdetails-beitraege">
<script type="text/javascript">

var beitragHoehe = new Array();
{foreach from=$beitraege item=beitrag}
beitragHoehe[{$beitrag.beitragid}] = "{if $beitrag.hoehe != null}{$beitrag.hoehe}{else}{$mitglied.latest.beitrag}{/if}";
{/foreach}

{literal}
function changeBeitragNeuHoehe(beitragID) {
	if (beitragHoehe[beitragID]) {
		document.getElementById("beitrag_neu_hoehe").value = beitragHoehe[beitragID];
	}
}

function showMitgliederBeitraegePane(text) {
	$(".mitgliederbeitrag").css("display", "none");
	$("#mitgliederbeitrag_" + text).css("display", "block");
}

$(function () {
	showMitgliederBeitraegePane("overview");
});
{/literal}

</script>

			<div id="mitgliederbeitrag_overview" class="mitgliederbeitrag">
				<div class="btn-toolbar">
					<div class="btn-group">
						<a data-toggle="modal" href="#beitrag-add" class="btn btn-primary">Beitrag hinzufügen</a>
					</div>
				</div>
				<form action="{"mitglieder_beitraege"|___:$mitglied.mitgliedid}" method="post" class="form-horizontal modal" id="beitrag-add" style="display:none;">
					<div class="modal-header">
						<a class="close" data-dismiss="modal">×</a>
						<h3>Beitrag hinzufügen</h3>
					</div>
					<fieldset class="modal-body">
						<div class="control-group">
							<label class="control-label" for="beitrag_neu_beitragid">{"Beitrag:"|__}</label>
							<div class="controls">
								<select name="beitrag_neu_beitragid" onchange="changeBeitragNeuHoehe(this.value);">
									<option value="">&nbsp;</option>
									{foreach from=$beitraege item=beitrag}
										{assign var=beitragid value=$beitrag.beitragid}
										{if !isset($mitglied.beitraege.$beitragid)}
											<option value="{$beitrag.beitragid|escape:html}">{$beitrag.label|escape:html}</option>
										{/if}
									{/foreach}
								</select>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="beitrag_neu_hoehe">{"Höhe:"|__}</label>
							<div class="controls">
								<div class="input-append">
									<input type="text" class="input-small" name="beitrag_neu_hoehe" id="beitrag_neu_hoehe" />
									<span class="add-on">EUR</span>
								</div>
							</div>
						</div>
					</fieldset>
					<div class="modal-footer">
						<button class="btn btn-success submit" type="submit" name="neu" value="1">{"Anlegen"|__}</button>
					</div>
				</form>
				<form action="{"mitglieder_beitraege"|___:$mitglied.mitgliedid}" method="post" class="form-inline">
					<fieldset>
						<table class="table table-striped">
							<thead>
								<tr>
									<th>&nbsp;</th>
									<th>Beitrag</th>
									<th>Bezahlt</th>
									<th>Ausstehend</th>
								</tr>
							</thead>
							{foreach from=$mitglied.beitraege item=beitrag}
								<tr>
									<th><a href="javascript:showMitgliederBeitraegePane('{$beitrag.beitrag.beitragid}')">{$beitrag.beitrag.label|escape:html}</a></th>
									<td>
										<div class="input-append">
											<input type="text" class="input-small" name="beitraege_hoehe[{$beitrag.beitrag.beitragid}]" value="{$beitrag.hoehe|string_format:"%.2f"}" />
											<span class="add-on">EUR</span>
										</div>
									</td>
									<td>{$beitrag.bezahlt|string_format:"%.2f"} EUR</td>
									<td>{$beitrag.hoehe-$beitrag.bezahlt|string_format:"%.2f"} EUR</td>
									<td>
										<a href="{"mitglieder_beitraege_del"|___:$beitrag.mitgliederbeitragid}" class="close delete">&times;</a>
									</td>
								</tr>
							{/foreach}
							<tfoot>
								<tr>
									<th>Summe</th>
									<th>{$mitglied.beitraege_hoehe|string_format:"%.2f"} EUR</th>
									<th>{$mitglied.beitraege_bezahlt|string_format:"%.2f"} EUR</th>
									<th>{$mitglied.beitraege_hoehe-$mitglied.beitraege_bezahlt|string_format:"%.2f"} EUR</th>
									<th>&nbsp;</th>
								</tr>
							</tfoot>
						</table>
						<button class="btn btn-primary submit" type="submit" name="save" value="1">{"Speichern"|__}</button>
					</fieldset>
				</form>
			</div>

			{foreach from=$mitglied.beitraege item=beitrag}
				<div id="mitgliederbeitrag_{$beitrag.beitrag.beitragid}" class="mitgliederbeitrag">
					<div class="btn-toolbar">
						<div class="btn-group">
							<button onClick="showMitgliederBeitraegePane('overview');" class="btn"><i class="icon-arrow-left"></i> Übersicht</button>
						</div>
						<div class="btn-group">
							<a data-toggle="modal" href="#beitrag{$beitrag.mitgliederbeitragid}-buchung-add" class="btn btn-primary">Buchung hinzufügen</a>
						</div>
					</div>
					<h2>{$beitrag.beitrag.label|escape:html}</h2>
					<form action="{"mitglieder_beitraege_buchungen"|___:$beitrag.mitgliederbeitragid}" method="post" class="form-horizontal modal" id="beitrag{$beitrag.mitgliederbeitragid}-buchung-add" style="display:none;">
						<div class="modal-header">
							<a class="close" data-dismiss="modal">×</a>
							<h3>Buchung hinzufügen</h3>
						</div>
						<fieldset class="modal-body">
							<div class="control-group">
								<label class="control-label" for="timestamp">{"Erhalten:"|__}</label>
								<div class="controls">
									<input type="text" class="span2" name="timestamp" value="{$smarty.now|date_format:"%d.%m.%Y"}" />
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="gliederungid">{"Gliederung:"|__}</label>
								<div class="controls">
									{include file="gliederungdropdown.block.tpl" fieldname="gliederungid" gliederungen=$gliederungen selectedgliederungid=$session->getDefaultGliederungID()}
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="hoehe">{"Betrag:"|__}</label>
								<div class="controls">
									<div class="input-append">
										<input type="text" class="input-small" name="hoehe" value="{$beitrag.hoehe-$beitrag.bezahlt}" />
										<span class="add-on">EUR</span>
									</div>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="mailtemplateid">{"Versende Bestätigung:"|__}</label>
								<div class="controls">
									<select name="mailtemplateid">
									         <option name="">{"(keine)"|__}</option>
									         {foreach from=$mailtemplates item=mailtemplate}
											<option value="{$mailtemplate.templateid|escape:html}"
											        {if $beitrag.beitrag.mailtemplate.templateid == $mailtemplate.templateid}selected="selected"{/if}>
												{$mailtemplate.label|escape:html}
											</option>
										{/foreach}
									</select>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="vermerk">{"Vermerk:"|__}</label>
								<div class="controls">
									<textarea name="vermerk" cols="40" rows="2"></textarea>
								</div>
							</div>
						</fieldset>
						<div class="modal-footer">
							<button class="btn btn-success submit" type="submit" name="add" value="1">{"Hinzufügen"|__}</button>
						</div>
					</form>
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Datum</th>
								<th>Bearbeiter</th>
								<th>Gliederung</th>
								<th>Vermerk</th>
								<th>Betrag</th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						{foreach from=$beitrag.buchungen item=buchung}
							<tr>
								<td>{if isset($buchung.timestamp)}{$buchung.timestamp|date_format:"%d.%m.%Y"}{/if}</td>
								<td>{if isset($buchung.user)}{$buchung.user.username|escape:html}{/if}</td>
								<td>{$buchung.gliederung.label|escape:html}</td>
								<td>{$buchung.vermerk|escape:html}</td>
								<td>{$buchung.hoehe|string_format:"%.2f"} EUR</td>
								<td><a href="{"mitglieder_beitraege_buchungen_del"|___:$buchung.buchungid}" class="close delete">&times;</a></td>
							</tr>
						{/foreach}
						<tfoot>
							<tr>
								<th colspan="4">Bezahlt {$beitrag.beitrag.label|escape:html}</th>
								<th>{$beitrag.bezahlt|string_format:"%.2f"} EUR</th>
								<th>&nbsp;</th>
							</tr>
							<tr>
								<th colspan="4">Beitrag {$beitrag.beitrag.label|escape:html}</th>
								<th>{$beitrag.hoehe|string_format:"%.2f"} EUR</th>
								<th>&nbsp;</th>
							</tr>
							<tr>
								<th colspan="4">Ausstehend {$beitrag.beitrag.label|escape:html}</th>
								<th>{$beitrag.hoehe-$beitrag.bezahlt|string_format:"%.2f"} EUR</th>
								<th>&nbsp;</th>
							</tr>
						</tfoot>
					</table>
				</div>
			{/foreach}
		</div>
	</div>
</div>
{include file="footer.html.tpl"}
