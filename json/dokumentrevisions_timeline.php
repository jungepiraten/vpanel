<?php

require_once(dirname(__FILE__) . "/../config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();

if (!$session->isSignedIn()) {
	exit;
}

$pagesize = 10;
$offset = $session->getVariable("page") * $pagesize;

$revisions = $session->getStorage()->getDokumentRevisionListTimeline($session->getAllowedGliederungIDs("dokumente_show"), $offset, $pagesize);
$jsons = array();

foreach ($revisions as $revision) {
	$row = array();
	$row["timestamp"] = $revision->getTimestamp();
	$row["revisionid"] = $revision->getRevisionID();
	$row["dokumentid"] = $revision->getDokumentID();
	$row["dokumentidentifier"] = $revision->getIdentifier();
	$row["dokumentlabel"] = $revision->getLabel();
	$row["userid"] = $revision->getUserID();
	$row["username"] = $revision->getUser()->getUsername();
	$row["location"] = $session->getLink("dokumente_details", $revision->getDokumentID());

	$jsons[] = $row;
}

print(json_encode($jsons));

?>
