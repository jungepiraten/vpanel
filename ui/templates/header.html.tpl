<html>
<head>
<link rel="stylesheet" type="text/css" href="ui/style.css" />
<meta http-equiv="Content-Type" content="text/html; charset={$charset}" />
<title>VPANEL</title>
</head>
<body>
<div id="navigation">
 <a href="{"index"|___}">{"Startseite"|__}</a>
{assign var=auth value=$session->getAuth()}
{if $auth->isAllowed("users_show")}
 <a href="{"users"|___}">{"Benutzerverwaltung"|__}</a>
{/if}
{if $auth->isAllowed("roles_show")}
 <a href="{"roles"|___}">{"Rollenverwaltung"|__}</a>
{/if}
{if $auth->isSignedIn()}
 <a href="{"logout"|___}">{"Abmelden"|__}</a>
{else}
 <a href="{"login"|___}">{"Anmelden"|__}</a>
{/if}
</div>
