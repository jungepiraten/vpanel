<?php

require_once(dirname(__FILE__) . "/../config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();

if (!$session->isSignedIn()) {
	exit;
}

$pagesize = 10;
$offset = $session->getVariable("page") * $pagesize;

$buchungen = $session->getStorage()->getMitgliederBeitragBuchungListTimeline($session->getAllowedGliederungIDs("mitglieder_show"), $offset, $pagesize);
$jsons = array();

foreach ($buchungen as $buchung) {
	$row = array();
	$row["timestamp"] = $buchung->getTimestamp();
	$row["buchungid"] = $buchung->getBuchungID();
	$row["beitragid"] = $buchung->getMitgliederBeitrag()->getBeitragID();
	$row["beitraglabel"] = $buchung->getMitgliederBeitrag()->getBeitrag()->getLabel();
	$row["mitgliedid"] = $buchung->getMitgliederBeitrag()->getMitgliedID();
	$row["mitgliedlabel"] = $buchung->getMitgliederBeitrag()->getMitglied()->replaceText("{BEZEICHNUNG}");
	$row["userid"] = $buchung->getUserID();
	$row["username"] = $buchung->getUser()->getUsername();
	$row["gliederungid"] = $buchung->getGliederungID();
	$row["gliederunglabel"] = $buchung->getGliederung()->getLabel();
	$row["betrag"] = $buchung->getHoehe();
	$row["location"] = $session->getLink("mitglieder_details", $buchung->getMitgliederBeitrag()->getMitgliedID());
	$jsons[] = $row;
}

print(json_encode($jsons));

?>
