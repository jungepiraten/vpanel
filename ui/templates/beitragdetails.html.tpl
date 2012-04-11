{capture assign="ansicht"}Beitrag <em>&raquo;{$beitrag.label}&laquo;</em> bearbeiten{/capture}
{include file="header.html.tpl" ansicht=$ansicht menupunkt="beitrag"}
{include file="beitragform.block.tpl" beitrag=$beitrag}
{if $session->isAllowed("mitglieder_show")}
	{include file="beitragdetails.buttons.tpl" page=$mitgliederbeitraglist_page pagecount=$mitgliederbeitraglist_pagecount}
	<table class="table table-striped table-bordered table-condensed">
		<thead>
			<tr>
				<th>#</th>
				<th>Name</th>
				<th>Beitrag</th>
				<th>Bezahlt</th>
				<th>Offen</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		{foreach from=$mitgliederbeitraglist item=mitgliederbeitrag}
			<tr onclick="doNav('{"mitglieder_details"|___:$mitgliederbeitrag.mitglied.mitgliedid}');" {if isset($mitgliederbeitrag.mitglied.austritt)}id="ex"{/if}>
				<td>{$mitgliederbeitrag.mitglied.mitgliedid|escape:html}</td>
				<td>
					{include file="mitgliederlist.item.tpl" mitglied=$mitgliederbeitrag.mitglied}
				</td>
				<td>{$mitgliederbeitrag.hoehe|string_format:"%.2f"}</td>
				<td>{$mitgliederbeitrag.bezahlt|string_format:"%.2f"}</td>
				<td>{$mitgliederbeitrag.hoehe-$mitgliederbeitrag.bezahlt|string_format:"%.2f"}</td>
				<td><a class="close delete" href="{"mitglieder_beitraege_del"|___:$mitgliederbeitrag.mitgliederbeitragid}">&times;</a></td>
			</tr>
		{/foreach}
	</table>
	{include file="beitragdetails.buttons.tpl" page=$mitgliederbeitraglist_page pagecount=$mitgliederbeitraglist_pagecount}
{/if}
{include file="footer.html.tpl"}
