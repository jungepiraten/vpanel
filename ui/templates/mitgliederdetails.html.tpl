{capture assign="ansicht"}Mitglied #{$mitglied.mitgliedid} ({if isset($mitglied.latest.natperson)}{$mitglied.latest.natperson.vorname|escape:html} {$mitglied.latest.natperson.name|escape:html}{/if}{if isset($mitglied.latest.jurperson)}{$mitglied.latest.jurperson.label|escape:html}{/if}) bearbeiten{/capture}
{include file="header.html.tpl" ansicht=$ansicht menupunkt="mitglied"}

{include file=mitgliederbadges.block.tpl badges=$mitglied.badges}

<div class="tabbable">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#mitgliederdetails-kartei" data-toggle="tab">{"Kartei"|__}</a></li>
		<li><a href="#mitgliederdetails-historie" data-toggle="tab">{"Historie"|__}</a></li>
		<li><a href="#mitgliederdetails-links" data-toggle="tab">{"Verknüpfungen"|__}</a></li>
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
		<div class="tab-pane" id="mitgliederdetails-historie">
			{foreach from=$mitgliedrevisions item=revision}
				<div class="well">
					<ul style="font-size:0.8em; list-style-type:square;">
						<li class="meta">{if isset($revision.user)}{"Von %s"|__:$revision.user.username}{/if} {include file="timestamp.tpl" timestamp=$revision.timestamp}</li>
						{if !isset($revisiongliederung) || $revisiongliederung != $revision.gliederung.gliederungid}{assign var=revisiongliederung value=$revision.gliederung.gliederungid}
							<li>{"Gliederung auf %s gesetzt"|__:$revision.gliederung.label}</li>{/if}
						{if !isset($revisionmitgliedschaft) || $revisionmitgliedschaft != $revision.mitgliedschaft.mitgliedschaftid}{assign var=revisionmitgliedschaft value=$revision.mitgliedschaft.mitgliedschaftid}
							<li>{"Mitgliedschaft auf %s gesetzt"|__:$revision.mitgliedschaft.label}</li>{/if}
						{if isset($revision.natperson) && (!isset($revisionnatperson) || $revisionnatperson != isset($revision.natperson))}{assign var=revisionnatperson value=isset($revision.natperson)}
							<li>{"Auf Natürliche Person gesetzt"|__}</li>
							{assign var=revisionnatanrede value=$revision.natperson.anrede}
							{assign var=revisionnatvorname value=$revision.natperson.vorname}
							{assign var=revisionnatname value=$revision.natperson.name}
							<li>{"Name auf %s %s %s gesetzt"|__:$revision.natperson.anrede:$revision.natperson.vorname:$revision.natperson.name}</li>
							{assign var=revisionnatgeburt value=$revision.natperson.geburtsdatum}
							<li>{"Geburtsdatum auf %s gesetzt"|__:$revision.natperson.geburtsdatum}</li>
							{assign var=revisionnatnationalitaet value=$revision.natperson.nationalitaet}
							{if $revision.natperson.nationalitaet != ""}<li>{"Nationalitaet auf %s gesetzt"|__:$revision.natperson.nationalitaet}</li>{/if}
						{else}
							{if $revisionnatanrede != $revision.natperson.anrede
							 || $revisionnatvorname != $revision.natperson.vorname
							 || $revisionnatname != $revision.natperson.name}
							 	{assign var=revisionnatanrede value=$revision.natperson.anrede}
							 	{assign var=revisionnatvorname value=$revision.natperson.vorname}
							 	{assign var=revisionnatname value=$revision.natperson.name}
								<li>{"Name auf %s %s %s gesetzt"|__:$revision.natperson.anrede:$revision.natperson.vorname:$revision.natperson.name}</li>{/if}
							{if $revisionnatgeburt != $revision.natperson.geburtsdatum}{assign var=revisionnatgeburt value=$revision.natperson.geburtsdatum}
								<li>{"Geburtsdatum auf %s gesetzt"|__:$revision.natperson.geburtsdatum}</li>{/if}
							{if $revisionnatnationalitaet != $revision.natperson.nationalitaet}{assign var=revisionnatnationalitaet value=$revision.natperson.nationalitaet}
								<li>{"Nationalitaet auf %s gesetzt"|__:$revision.natperson.nationalitaet}</li>{/if}	
						{/if}
						{if isset($revision.jurperson) && (!isset($revisionjurperson) || $revisionjurperson != isset($revision.jurperson))}{assign var=revisionjurperson value=isset($revision.jurperson)}
							<li>{"Auf Juristische Person gesetzt"|__}</li>
							{assign var=revisionjurpersonlabel value=$revision.jurperson.label}
							<li>{"Firma auf %s gesetzt"|__:$revision.jurperson.label}</li>
						{else}
							{if $revisionjurpersonlabel != $revision.jurperson.label}{assign var=revisionjurpersonlabel value=$revision.jurperson.label}
								<li>{"Firma auf %s gesetzt"|__:$revision.jurperson.label}</li>{/if}
						{/if}
						{if !isset($revisionadresszusatz) || $revisionadresszusatz != $revision.kontakt.adresszusatz
						 || !isset($revisionstrasse) || $revisionstrasse != $revision.kontakt.strasse
						 || !isset($revisionhausnummer) || $revisionhausnummer != $revision.kontakt.hausnummer
						 || !isset($revisionort) || $revisionort != $revision.kontakt.ort.ortid}
							{assign var=revisionadresszusatz value=$revision.kontakt.adresszusatz}
							{assign var=revisionstrasse value=$revision.kontakt.strasse}
							{assign var=revisionhausnummer value=$revision.kontakt.hausnummer}
							{assign var=revisionort value=$revision.kontakt.ort.ortid}
							{if $revision.kontakt.adresszusatz != ""}
								<li>{"Adresse auf %s, %s %s, %s %s in %s gesetzt"|__:$revision.kontakt.adresszusatz:$revision.kontakt.strasse:$revision.kontakt.hausnummer:$revision.kontakt.ort.plz:$revision.kontakt.ort.label:$revision.kontakt.ort.state.label}</li>
							{else}
								<li>{"Adresse auf %s %s, %s %s in %s gesetzt"|__:$revision.kontakt.strasse:$revision.kontakt.hausnummer:$revision.kontakt.ort.plz:$revision.kontakt.ort.label:$revision.kontakt.ort.state.label}</li>
							{/if}
						{/if}
						{if $revision.kontakt.telefon != ""}
							{if !isset($revisiontelefon) || $revisiontelefon != $revision.kontakt.telefon}{assign var=revisiontelefon value=$revision.kontakt.telefon}
								<li>{"Telefonnummer auf %s gesetzt"|__:$revision.kontakt.telefon}</li>{/if}
						{else}
							{if isset($revisiontelefon) && $revisiontelefon != $revision.kontakt.telefon}{assign var=revisiontelefon value=$revision.kontakt.telefon}
								<li>{"Telefonnummer entfernt"|__:$revision.kontakt.telefon}</li>{/if}
						{/if}
						{if $revision.kontakt.handy != ""}
							{if !isset($revisionhandy) || $revisionhandy != $revision.kontakt.handy}{assign var=revisionhandy value=$revision.kontakt.handy}
								<li>{"Handynummer auf %s gesetzt"|__:$revision.kontakt.handy}</li>{/if}
						{else}
							{if isset($revisionhandy) && $revisionhandy != $revision.kontakt.handy}{assign var=revisionhandy value=$revision.kontakt.handy}
								<li>{"Handynummer entfernt"|__:$revision.kontakt.handy}</li>{/if}
						{/if}
						{if $revision.kontakt.email != ""}
							{if !isset($revisionemail) || $revisionemail != $revision.kontakt.email.email}{assign var=revisionemail value=$revision.kontakt.email.email}
								<li>{"EMail-Adresse auf %s gesetzt"|__:$revision.kontakt.email.email}</li>{/if}
						{else}
							{if isset($revisionemail) && $revisionemail != $revision.kontakt.email.email}{assign var=revisionemail value=$revision.kontakt.email.email}
								<li>{"EMail-Adresse entfernt"|__:$revision.kontakt.email.email}</li>{/if}
						{/if}
						{if $revision.kontakt.iban != ""}
							{if !isset($revisioniban) || $revisioniban != $revision.kontakt.iban}{assign var=revisioniban value=$revision.kontakt.iban}
								<li>{"Bankverbindung auf %s gesetzt"|__:$revision.kontakt.iban}</li>{/if}
						{else}
							{if isset($revisioniban) && $revisioniban != $revision.kontakt.iban}{assign var=revisioniban value=$revision.kontakt.iban}
								<li>{"Bankverbindung entfernt"|__:$revision.kontakt.iban}</li>{/if}
						{/if}
						{if !isset($revisionbeitrag) || $revisionbeitrag != $revision.beitrag
						 || !isset($revisionbeitragtimeformat) || $revisionbeitragtimeformat != $revision.beitragtimeformat.beitragtimeformatid}
							{assign var=revisionbeitrag value=$revision.beitrag}
							{assign var=revisionbeitragtimeformat value=$revision.beitragtimeformat.beitragtimeformatid}
							<li>{"Beitrag auf %.2f EUR %s gesetzt"|__:$revision.beitrag:$revision.beitragtimeformat.label}</li>{/if}
						{if !isset($revisiongeloescht) || $revision.geloescht != $revisiongeloescht}{assign var=revisiongeloescht value=$revision.geloescht}
							{if $revision.geloescht}
								<li>{"Als Ausgetreten markiert"|__}</li>{else}
								<li>{"Austrittsflag entfernt"|__}</li>{/if}
						{/if}
						{foreach from=$revision.flags key=flagid item=flag}{if !isset($revisionflags) || $revision.flags.$flagid != $revisionflags.$flagid}
							<li>{"%s hinzugefügt"|__:$flag.label}</li>{/if}{/foreach}
						{if isset($revisionflags)}{foreach from=$revisionflags key=flagid item=flag}{if $revision.flags.$flagid != $revisionflags.$flagid}
							<li>{"%s entfernt"|__:$flag.label}</li>{/if}{/foreach}{/if}
						{assign var=revisionflags value=$revision.flags}
						{foreach from=$revision.textfields key=textfieldid item=textfield}
							{if $textfield.value != ""}
								{if !isset($revisiontextfields.$textfieldid) || $revisiontextfields.$textfieldid.value != $textfield.value}
									<li>{"%s auf %s gesetzt"|__:$textfield.textfield.label:$textfield.value}</li>{/if}
							{else}
								{if isset($revisiontextfields.$textfieldid) && $revisiontextfields.$textfieldid.value != $textfield.value}
									<li>{"%s entfernt"|__:$textfield.textfield.label:$textfield.value}</li>{/if}
							{/if}
						{/foreach}
						{if isset($revisiontextfields)}{foreach from=$revisiontextfields key=textfieldid item=textfield}
							{if !isset($revision.textfields.$textfieldid)}
								<li>{"%s entfernt"|__:$textfield.textfield.label:$textfield.value}</li>{/if}
						{/foreach}{/if}
						{assign var=revisiontextfields value=$revision.textfields}
					</ul>
					<div class="kommentar" style="margin-top:0.5em;">{$revision.kommentar}</div>
				</div>
			{/foreach}
		</div>
		<div class="tab-pane" id="mitgliederdetails-links">
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
									<input type="date" name="timestamp" value="{$smarty.now|date_format:"%Y-%m-%d"}" />
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
