{capture assign="ansicht"}Mitglied #{$mitglied.mitgliedid} ({if isset($mitglied.latest.natperson)}{$mitglied.latest.natperson.vorname|escape:html} {$mitglied.latest.natperson.name|escape:html}{/if}{if isset($mitglied.latest.jurperson)}{$mitglied.latest.jurperson.label|escape:html}{/if}) bearbeiten{/capture}
{include file="header.html.tpl" ansicht=$ansicht menupunkt="mitglied"}

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

<table>
	<tr>
		<th>Eingetreten</th>
		<td>{$mitglied.eintritt|date_format:"%d.%m.%Y"}</td>
	</tr>
	{if isset($mitglied.austritt)}
		<tr>
			<th>Ausgetreten</th>
			<td>{$mitglied.austritt|date_format:"%d.%m.%Y"}</td>
		</tr>
	{/if}
</table>

{include file="mitgliederform.block.tpl" mitglied=$mitglied}

<div class="container-fluid">
<div class="row-fluid">

	<div class="span4">
	{foreach from=$mitgliednotizen item=notiz}
		<div class="well">
			<span class="meta">{if isset($notiz.author)}{"Von %s"|__:$notiz.author.username}{/if}</span>
			<div class="kommentar">{$notiz.kommentar}</div>
		</div>
	{/foreach}
	<form action="{"mitglieder_details"|___:$mitglied.mitgliedid}" method="post">
		<fieldset>
			<div class="controls">
				<textarea cols="35" rows="2" name="kommentar"></textarea>
			</div>
			<button class="btn btn-success submit" type="submit" name="addnotiz" value="1">{"Hinzufügen"|__}</button>
		</fieldset>
	</form>
	<div class="btn-toolbar">
		<div class="btn-group">
			<a href="{"mitglieder_dokument"|___:$mitglied.mitgliedid}" class="btn">Dokument verlinken</a>
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

	<div class="span8">
<script type="text/javascript">

var beitragHoehe = new Array();
{foreach from=$beitraege item=beitrag}
beitragHoehe[{$beitrag.beitragid}] = "{if $beitrag.hoehe != null}{$beitrag.hoehe}{else}{$mitglied.latest.beitrag}{/if}";
{/foreach}

{literal}
function changeBeitragNeuHoehe(beitragID) {
	document.getElementById("beitrag_neu_hoehe").value = beitragHoehe[beitragID];
}
{/literal}

</script>
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
						<th><a href="{"beitraege_details"|___:$beitrag.beitrag.beitragid}">{$beitrag.beitrag.label|escape:html}</a></th>
						<td>
							<div class="input-append">
								<input type="text" class="span1" name="beitraege_hoehe[{$beitrag.beitrag.beitragid}]" value="{$beitrag.hoehe|string_format:"%.2f"}" />
								<span class="add-on">EUR</span>
							</div>
						</td>
						<td>
							<div class="input-append">
								<input type="text" class="span1" name="beitraege_bezahlt[{$beitrag.beitrag.beitragid}]" value="{$beitrag.bezahlt|string_format:"%.2f"}" />
								<span class="add-on">EUR</span>
							</div>
						</td>
						<td>{$beitrag.hoehe-$beitrag.bezahlt|string_format:"%.2f"} EUR</td>
						<td><a href="{"mitglieder_beitraege_del"|___:$mitglied.mitgliedid:$beitrag.beitrag.beitragid}" class="delimg">&nbsp;</a></td>
					</tr>
				{/foreach}
				{if count($beitraege) > count($mitglied.beitraege)}
					<tr>
						<th>
							<select name="beitrag_neu_beitragid" onchange="changeBeitragNeuHoehe(this.value);">
								<option value="">{"(nichts hinzufügen)"|__}</option>
								{foreach from=$beitraege item=beitrag}
								{assign var=beitragid value=$beitrag.beitragid}
								{if !isset($mitglied.beitraege.$beitragid)}
									<option value="{$beitrag.beitragid|escape:html}">{$beitrag.label|escape:html}</option>
								{/if}
								{/foreach}
							</select>
						</th>
						<td>
							<div class="input-append">
								<input type="text" class="span1" name="beitrag_neu_hoehe" id="beitrag_neu_hoehe" />
								<span class="add-on">EUR</span>
							</div>
						</td>
						<td>
							<div class="input-append">
								<input type="text" class="span1" name="beitrag_neu_bezahlt" />
								<span class="add-on">EUR</span>
							</div>
						</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				{/if}
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
</div>
</div>
{include file="footer.html.tpl"}
