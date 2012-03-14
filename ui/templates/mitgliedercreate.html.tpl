{capture assign="ansicht"}Neues <em>&raquo;{$mitgliedschaft.label|escape:html}&laquo;</em> anlegen{/capture}
{include file="header.html.tpl" ansicht=$ansicht menupunkt="mitglied"}
{include file="mitgliederform.block.tpl" mitgliedschaft=$mitgliedschaft}
{include file="footer.html.tpl"}
