{capture assign="ansicht"}Neues {if isset($mitgliedtemplate)}<em>&raquo;{$mitgliedtemplate.label|escape:html}&laquo;</em>{else}Mitglied{/if} anlegen{/capture}
{include file="header.html.tpl" ansicht=$ansicht menupunkt="mitglied"}
{include file="mitgliederform.block.tpl" mitgliedschaft=$mitgliedschaft}
{include file="footer.html.tpl"}
