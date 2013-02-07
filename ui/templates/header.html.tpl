<!DOCTYPE html> 
<html>
<head>
<link href="ui/bootstrap/css/bootstrap.css" rel="stylesheet" />
<link rel="icon" type="image/png" href="ui/images/favicon.png" />
<link rel="stylesheet" type="text/css" href="ui/style.css" />
<link rel="stylesheet" type="text/css" href="ui/fontawesome/css/font-awesome.min.css" />
<meta http-equiv="Content-Type" content="text/html; charset={$charset}" />
<script type="text/javascript" src="ui/jquery-1.7.1.js"></script>
<script type="text/javascript" src="ui/jquery.timeago.js"></script>
<script type="text/javascript" src="ui/jquery.timeago.de.js"></script>
<script type="text/javascript" src="ui/bootstrap/js/bootstrap.js"></script>
{literal}<script type="text/javascript">
function doNav(url) {
	if (event.which != 1) return;
	document.location.href = url;
}
</script>{/literal}
<title>{$ansicht|strip_tags:false} &bull; VPanel</title>
</head>
<body>
<div class="container-fluid">
	<div class="navbar navbar-fixed-top navbar-inverse">
		<div class="navbar-inner">
			<div class="container-fluid">
				<a class="brand" href="{"index"|___}"><span style="color: #F7931E; font-weight: bold;">V</span>Panel</a>
				<ul class="nav pull-right">
				{if $session->isSignedIn()}
					<li><a href="{"einstellungen"|___}">{"Einstellungen"|__}</a></li>
					<li class="divider-vertical"></li>
					<li><a href="{"logout"|___}">{"Abmelden"|__}</a></li>
				{else}
					<li><a href="{"login"|___}">{"Anmelden"|__}</a></li>
				{/if}
				</ul>
			</div>
		</div>
	</div>
	<div class="page-header">
		<h1>{$ansicht}</h1>
	</div>
	{if $session->isSignedIn()}
	<div class="row-fluid">
		<div class="span3">
			<div class="well sidebar-nav">
				<ul class="nav nav-list">
					<li {if $menupunkt == "dashboard"}class="active"{/if}><a href="{"index"|___}"><i class='icon-dashboard'></i> {"Dashboard"|__}</a></li>
					{if $session->isAllowed("users_show")}
						<li {if $menupunkt == "user"}class="active"{/if}><a href="{"users"|___}"><i class='icon-user'></i> {"Benutzer"|__}</a></li>
					{/if}
					{if $session->isAllowed("roles_show")}
						<li {if $menupunkt == "role"}class="active"{/if}><a href="{"roles"|___}"><i class='icon-tags'></i> {"Rollen"|__}</a></li>
					{/if}
					{if $session->isAllowed("dokumente_show")}
						<li {if $menupunkt == "dokument"}class="active"{/if}><a href="{"dokumente"|___}"><i class='icon-file-alt'></i> {"Dokumente"|__}</a></li>
					{/if}
					{if $session->isAllowed("mitglieder_show")}
						<li {if $menupunkt == "mitglied"}class="active"{/if}><a href="{"mitglieder"|___}"><i class='icon-group'></i> {"Mitglieder"|__}</a></li>
					{/if}
					{if $session->isAllowed("beitraege_show")}
						<li {if $menupunkt == "beitrag"}class="active"{/if}><a href="{"beitraege"|___}"><i class='icon-money'></i> {"Beiträge"|__}</a></li>
					{/if}
					{if $session->isAllowed("mailtemplates_show")}
						<li {if $menupunkt == "mail"}class="active"{/if}><a href="{"mailtemplates"|___}"><i class='icon-envelope'></i> {"Mails"|__}</a></li>
					{/if}
					{if $session->isAllowed("stats_show")}
						<li {if $menupunkt == "stats"}class="active"{/if}><a href="{"stats"|___}"><i class='icon-bar-chart'></i> {"Mitgliederstats"|__}</a></li>
					{/if}
					{if $session->isAllowed("dokumente_show")}
						<li class="nav-header">Dokumentensuche</li>
						<li><form action="{"dokumente"|___}">{include file="dokument.suche.block.tpl"}</form></li>
					{/if}
					{if $session->isAllowed("mitglieder_show")}
						<li class="nav-header">Mitgliedersuche</li>
						<li><form action="{"mitglieder"|___}">{include file="mitglieder.suche.block.tpl"}</form></li>
					{/if}
				</ul>
			</div>
		</div>
		<div class="span9">
	{else}
	<div class="content">
	{/if}

