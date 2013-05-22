<?php

require_once(dirname(__FILE__) . "/../config.inc.php");

$session = $config->getSession(true);
$api = $session->getTemplate();

if (!$session->isSignedIn()) {
	$api->output(array("failed" => "AUTH_MISSING"), 401);
	exit;
}

$mitglied = $session->getStorage()->getMitglied($session->getVariable("mitgliedid"));

if ($mitglied == null) {
	$api->output(array("failed" => "MITGLIED_MISSING"), 400);
	exit;
}

if (!$session->isAllowed("mitglieder_show", $mitglied->getLatestRevision()->getGliederungID())) {
	$api->output(array("failed" => "PERMISSION_DENIED"), 403);
	exit;
}

$api->output(array("mitglied" => $mitglied));

?>
