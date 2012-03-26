<!DOCTYPE html> 
<html>
<head>
<link href="ui/bootstrap/css/bootstrap.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="ui/style.css" />
<meta http-equiv="Content-Type" content="text/html; charset={$charset}" />
<script type="text/javascript" src="ui/jquery-1.7.1.js"></script>
<script type="text/javascript" src="ui/bootstrap/js/bootstrap.js"></script>
{literal}<script type="text/javascript">
function doNav(url) {
  document.location.href = url;
}
</script>{/literal}
<title>{$ansicht|strip_tags:false} &bull; VPanel</title>
</head>
<body>
<div class="container-fluid">
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container-fluid">
				<a class="brand" href="{"index"|___}"><span style="color:#ff8d00; font-weight:bolder;">V</span>Panel</a>
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
					{if $session->isAllowed("users_show")}
						<li {if $menupunkt == "user"}class="active"{/if}><a href="{"users"|___}">{"Benutzerverwaltung"|__}</a></li>
					{/if}
					{if $session->isAllowed("roles_show")}
						<li {if $menupunkt == "role"}class="active"{/if}><a href="{"roles"|___}">{"Rollenverwaltung"|__}</a></li>
					{/if}
					{if $session->isAllowed("dokumente_show")}
						<li {if $menupunkt == "dokument"}class="active"{/if}><a href="{"dokumente"|___}">{"Dokumentenverwaltung"|__}</a></li>
					{/if}
					{if $session->isAllowed("mitglieder_show")}
						<li {if $menupunkt == "mitglied"}class="active"{/if}><a href="{"mitglieder"|___}">{"Mitgliederverwaltung"|__}</a></li>
					{/if}
					{if $session->isAllowed("beitraege_show")}
						<li {if $menupunkt == "beitrag"}class="active"{/if}><a href="{"beitraege"|___}">{"Beitragsverwaltung"|__}</a></li>
					{/if}
					{if $session->isAllowed("mailtemplates_show")}
						<li {if $menupunkt == "mail"}class="active"{/if}><a href="{"mailtemplates"|___}">{"Mailverwaltung"|__}</a></li>
					{/if}
					{if $session->isAllowed("statistik_show")}
						<li {if $menupunkt == "statistik"}class="active"{/if}><a href="{"statistik"|___}">{"Mitgliederstatistik"|__}</a></li>
					{/if}
					{if $session->isAllowed("dokumente_show")}
						<li class="nav-header">Dokumentensuche</li>
						<li>{include file="dokument.suche.block.tpl"}</li>
					{/if}
					{if $session->isAllowed("mitglieder_show")}
						<li class="nav-header">Mitgliedersuche</li>
						<li>{include file="mitglieder.suche.block.tpl"}</li>
					{/if}
				</ul>
			</div>
		</div>
		<div class="span9">
	{else}
	<div class="content">
	{/if}

