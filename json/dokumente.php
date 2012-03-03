<?php

require_once(dirname(__FILE__) . "/../config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();

if (!$session->isSignedIn()) {
	exit;
}

$dokumente = $session->getStorage()->getDokumentSearchList($session->getAllowedGliederungIDs("dokumente_show"), explode(" ", $session->getVariable("q")), 5);
$jsons = array();

foreach ($dokumente AS $dokument) {
	$row = array();
	$row["dokumentid"] = $dokument->getDokumentID();
	$row["location"] = $session->getLink("dokumente_details", $dokument->getDokumentID());
	$row["label"] = $dokument->getIdentifier() . " " . $dokument->getLabel();
	
	$jsons[] = $row;
}

print(json_encode($jsons));

?>
