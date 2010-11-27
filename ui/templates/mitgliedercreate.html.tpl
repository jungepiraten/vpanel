{include file=header.html.tpl ansicht="Neues Mitglied anlegen"}
<p class="pagetitle">Neues <span name="titleart">{$mitgliedschaft.label|escape:html}</span> anlegen</p>
{include file=mitgliederform.block.tpl mitgliedschaft=$mitgliedschaft}
{include file=footer.html.tpl}
