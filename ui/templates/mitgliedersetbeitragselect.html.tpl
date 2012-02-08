{include file="header.html.tpl" ansicht="Beitrag setzen"}
<p class="pagetitle">{"Beitrag setzen"|__}</p>
<form action="{"mitglieder_setbeitrag.start"|___}" method="post">
 <fieldset>
  <input type="hidden" name="redirect" value="{if isset($smarty.post.redirect)}{$smarty.post.redirect|stripslashes|escape:html}{else}{$smarty.server.HTTP_REFERER|escape:html}{/if}" />
  <table>
  <tr>
   <th>{"Filter:"|__}</th>
   <td>
    <select name="filterid"><option value="">{"(kein Filter)"|__}</option>{foreach from=$filters item=filter}<option value="{$filter.filterid|escape:html}" {if $smarty.request.filterid == $filter.filterid}selected="selected"{/if}>{$filter.label|escape:html}</option>{/foreach}</select>
   </td>
  </tr>
  <tr>
   <th>{"Beitrag:"|__}</th>
   <td>
    <select name="beitragid">{foreach from=$beitraglist item=beitrag}<option value="{$beitrag.beitragid|escape:html}">{$beitrag.label|escape:html}</option>{/foreach}</select>
   </td>
  </tr>
  <tr>
   <td colspan="2"><input class="submit" type="submit" name="save" value="{"Start"|__}" /></td>
  </table>
 </fieldset>
</form>
{include file="footer.html.tpl"}
