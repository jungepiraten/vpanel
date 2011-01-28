<?php

require_once(dirname(__FILE__) . "/../config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();

if (!$session->isAllowed("mitglieder_show")) {
	exit;
}

$plz = $session->getVariable("plz");
$ort = $session->getVariable("ort");

$orte = $session->getStorage()->getOrtListLimit($plz, $ort, null, 5);
$jsons = array();

foreach ($orte as $ort) {
	$jsons[] = array("ortid" => $ort->getOrtID(), "plz" => $ort->getPLZ(), "ort" => $ort->getLabel(), "stateid" => $ort->getStateID());
}

print(json_encode($jsons));

?>
