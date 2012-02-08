{include file="header.html.tpl" ansicht="Beitrag bearbeiten"}
<p class="pagetitle">{"Beitrag \"%s\" bearbeiten"|__:$beitrag.label}</p>
{if $beitrag.hoehe != null}
{include file="beitragform.block.tpl" beitrag=$beitrag}
{/if}
{if $session->isAllowed("mitglieder_show")}
{include file="beitragdetails.buttons.tpl" page=$mitgliederbeitraglist_page pagecount=$mitgliederbeitraglist_pagecount}
<ul class="entrylist">
{foreach from=$mitgliederbeitraglist item=mitgliederbeitrag}
<li class="entry{cycle values="odd,even"}" {if isset($mitgliederbeitrag.mitglied.austritt)}id="ex"{/if}>
<a style="float:right; margin-top:7px;margin-right:7px;" href="{"mitglieder_beitraege_del"|___:$mitgliederbeitrag.mitglied.mitgliedid:$beitrag.beitragid}" class="delimg">&nbsp;</a>
<span style="float:right; margin-top:7px;margin-right:7px; width:50px;">{$mitgliederbeitrag.hoehe-$mitgliederbeitrag.bezahlt|string_format:"%.2f"}</span>
<span style="float:right; margin-top:7px;margin-right:7px; width:50px;">{$mitgliederbeitrag.bezahlt|string_format:"%.2f"}</span>
<span style="float:right; margin-top:7px;margin-right:7px; width:50px;">{$mitgliederbeitrag.hoehe|string_format:"%.2f"}</span>
{include file="mitgliederlist.item.tpl" mitglied=$mitgliederbeitrag.mitglied}
<div style="clear:both;"></div>
</li>
{/foreach}
</ul>
{include file="beitragdetails.buttons.tpl" page=$mitgliederbeitraglist_page pagecount=$mitgliederbeitraglist_pagecount}
{/if}
{include file="footer.html.tpl"}
