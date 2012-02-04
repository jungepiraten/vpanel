<html>
<head>
<link rel="stylesheet" type="text/css" href="ui/style.css" />
<meta http-equiv="Content-Type" content="text/html; charset={$charset}" />
<script type="text/javascript" src="ui/jquery-1.4.4.js"></script>
<script type="text/javascript" src="ui/jquery.progressbar.js"></script>
<title>{$ansicht} &bull; VPanel</title>
</head>
<body>
<div class="header">
<span class="logo"><span style="color:#ff8d00; font-weight:bolder; font-size:125%;">V</span>Panel</span>
<div class="login">
{if not $session->isSignedIn()}
 <a href="{"login"|___}">{"Anmelden"|__}</a>
{else}
 <a href="{"logout"|___}" class="logout">{"Abmelden"|__}</a>
{/if}
</div>
</div>
<div id="sidebar">
<div class="navigation">
<ul>
<li><a href="{"index"|___}">{"Startseite"|__}</a></li>
{if $session->isAllowed("users_show")}
<li><a href="{"users"|___}">{"Benutzerverwaltung"|__}</a></li>
{/if}
{if $session->isAllowed("roles_show")}
<li><a href="{"roles"|___}">{"Rollenverwaltung"|__}</a></li>
{/if}
{if $session->isAllowed("dokumente_show")}
<li><a href="{"dokumente"|___}">{"Dokumentenverwaltung"|__}</a></li>
{/if}
{if $session->isAllowed("mitglieder_show")}
<li><a href="{"mitglieder"|___}">{"Mitgliederverwaltung"|__}</a></li>
{/if}
{if $session->isAllowed("mailtemplates_show")}
<li><a href="{"mailtemplates"|___}">{"Mailverwaltung"|__}</a></li>
{/if}
{if $session->isAllowed("statistik_show")}
<li><a href="{"statistik"|___}">{"Mitgliederstatistik"|__}</a></li>
{/if}
</ul>
</div>
{if isset($sidebar)}<div class="sidebar">{include file=$sidebar}</div>{/if}
</div>

<div class="content">
