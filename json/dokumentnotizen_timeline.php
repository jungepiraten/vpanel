<?php

require_once(dirname(__FILE__) . "/../config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();

if (!$session->isSignedIn()) {
	exit;
}

$notizen = $session->getStorage()->getDokumentNotizListTimeline($session->getAllowedGliederungIDs("dokumente_show"), 0, 10);
$jsons = array();

foreach ($notizen AS $notiz) {
	$row = array();
	$row["timestamp"] = $notiz->getTimestamp();
	$row["notizid"] = $notiz->getDokumentNotizID();
	$row["dokumentid"] = $notiz->getDokumentID();
	$row["dokumentidentifier"] = $notiz->getDokument()->getIdentifier();
	$row["dokumentlabel"] = $notiz->getDokument()->getLabel();
	$row["userid"] = $notiz->getAuthorID();
	$row["username"] = $notiz->getAuthor()->getUsername();
	$row["location"] = $session->getLink("dokumente_details", $notiz->getDokumentID());

	$jsons[] = $row;
}

print(json_encode($jsons));

?>
