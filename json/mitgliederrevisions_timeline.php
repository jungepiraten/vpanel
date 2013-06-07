<?php

require_once(dirname(__FILE__) . "/../config.inc.php");
require_once(VPANEL_TEXTREPLACER . "/mitglied.class.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();

if (!$session->isSignedIn()) {
	exit;
}

$pagesize = 10;
$offset = $session->getVariable("page") * $pagesize;

$revisionen = $session->getStorage()->getMitgliederRevisionListTimeline($session->getAllowedGliederungIDs("mitglieder_show"), $offset, $pagesize);
$jsons = array();

foreach ($revisionen as $revision) {
	$mitgliedreplacer = new MitgliedTextReplacer($revision->getMitglied());

	$row = array();
	$row["timestamp"] = $revision->getTimestamp();
	$row["revisionid"] = $revision->getRevisionID();
	$row["mitgliedid"] = $revision->getMitgliedID();
	$row["mitgliedlabel"] = $mitgliedreplacer->replaceText("{BEZEICHNUNG}");
	$row["userid"] = $revision->getUserID();
	if ($revision->getUser() !== null) {
		$row["username"] = $revision->getUser()->getUsername();
	}
	$row["location"] = $session->getLink("mitglieder_details", $revision->getMitgliedID());
	$jsons[] = $row;
}

print(json_encode($jsons));

?>
