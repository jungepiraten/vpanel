{include file="header.html.tpl" ansicht="Mailvorlage bearbeiten"}
<p class="pagetitle">{"Mailvorlage #%d bearbeiten (%s)"|__:$mailtemplate.templateid:$mailtemplate.label}</p>
{include file="mailtemplateform.block.html" mailtemplate=$mailtemplate}
{include file="footer.html.tpl"}
