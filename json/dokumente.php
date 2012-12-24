<?php

require_once(dirname(__FILE__) . "/../config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();

if (!$session->isSignedIn()) {
	exit;
}

require_once(VPANEL_DOKUMENTMATCHER . "/logic.class.php");
require_once(VPANEL_DOKUMENTMATCHER . "/gliederung.class.php");
require_once(VPANEL_DOKUMENTMATCHER . "/search.class.php");

$matcher = new SearchDokumentMatcher(explode(' ', $session->getVariable("q")));
$matcher = new AndDokumentMatcher(new GliederungDokumentMatcher($session->getAllowedGliederungIDs("dokumente_show")), $matcher);

$dokumente = $session->getStorage()->getDokumentList($matcher, 5);
$jsons = array();

foreach ($dokumente AS $dokument) {
	$row = array();
	$row["dokumentid"] = $dokument->getDokumentID();
	$row["location"] = $session->getLink("dokumente_details", $dokument->getDokumentID());
	$row["label"] = $dokument->getLatestRevision()->getIdentifier() . " " . $dokument->getLatestRevision()->getLabel();

	$jsons[] = $row;
}

print(json_encode($jsons));

?>
