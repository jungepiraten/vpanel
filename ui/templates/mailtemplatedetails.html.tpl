{capture assign=ansicht}Mailvorlage <em>#{$mailtemplate.templateid} (&raquo;{$mailtemplate.label|escape:html}&laquo;)</em> bearbeiten{/capture}
{include file="header.html.tpl" ansicht=$ansicht menupunkt="mail"}
{include file="mailtemplateform.block.html" mailtemplate=$mailtemplate}
{include file="footer.html.tpl"}
