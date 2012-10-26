<?php

require_once(dirname(__FILE__) . "/../config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();

if (!$session->isSignedIn()) {
	exit;
}

require_once(VPANEL_MITGLIEDERMATCHER . "/logic.class.php");
require_once(VPANEL_MITGLIEDERMATCHER . "/gliederung.class.php");
require_once(VPANEL_MITGLIEDERMATCHER . "/search.class.php");

$matcher = new SearchMitgliederMatcher(explode(' ', $session->getVariable("q")));
$matcher = new AndMitgliederMatcher(new GliederungMitgliederMatcher($session->getAllowedGliederungIDs("mitglieder_show")), $matcher);

$mitglieder = $session->getStorage()->getMitgliederList($matcher, 5);
$jsons = array();

foreach ($mitglieder as $mitglied) {
	$revision = $mitglied->getLatestRevision();

	$row = array();
	$row["mitgliedid"] = $mitglied->getMitgliedID();
	$row["location"] = $session->getLink("mitglieder_details", $mitglied->getMitgliedID());
	if ($revision->isJurPerson()) {
		$row["label"] = $revision->getJurPerson()->getLabel();
	} else if ($revision->isNatPerson()) {
		$row["label"] = $revision->getNatPerson()->getVorname() . " " . $revision->getNatPerson()->getName();
	}

	$jsons[] = $row;
}

print(json_encode($jsons));

?>
