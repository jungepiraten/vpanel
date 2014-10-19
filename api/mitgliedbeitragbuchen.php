<?php

require_once(dirname(__FILE__) . "/../config.inc.php");

$session = $config->getSession(true);
$api = $session->getTemplate();

if (!$session->isSignedIn()) {
	$api->output(array("failed" => "AUTH_MISSING"), 401);
	exit;
}

$mitglied = $session->getStorage()->getMitglied($session->getVariable("mitgliedid"));
$beitrag = $session->getStorage()->getBeitrag($session->getVariable("beitragid"));
$timestamp = $session->getTimestampVariable("timestamp");
$gliederung = $session->getStorage()->getGliederung($session->getIntVariable("gliederungid"));
$vermerk = $session->getVariable("vermerk");
$hoehe = $session->getDoubleVariable("hoehe");

if ($mitglied == null) {
	$api->output(array("failed" => "MITGLIED_MISSING"), 400);
	exit;
}

if (!$session->isAllowed("mitglieder_modify", $mitglied->getLatestRevision()->getGliederungID())) {
	$api->output(array("failed" => "PERMISSION_DENIED"), 403);
	exit;
}

$mitgliedbeitrag = $session->getStorage()->getMitgliederBeitragByMitgliedBeitrag($mitglied->getMitgliedID(), $beitrag->getBeitragID());

if ($mitgliedbeitrag == null) {
	$api->output(array("failed" => "MITGLIEDBEITRAG_MISSING"));
	exit;
}

$buchung = new MitgliedBeitragBuchung($session->getStorage());
$buchung->setMitgliederBeitrag($mitgliedbeitrag);
$buchung->setGliederung($gliederung);
$buchung->setUser($session->getUser());
$buchung->setTimestamp($timestamp);
$buchung->setVermerk($vermerk);
$buchung->setHoehe($hoehe);
$buchung->save();

if ($beitrag->getMailTemplateID() !== null) {
	$mailtemplate = $beitrag->getMailTemplate();
	if ($mailtemplate != null) {
		$mail = $mailtemplate->generateMail($mitglied->getLatestRevision()->getKontakt()->getEMail(), new MitgliedTextReplacer($mitglied));
		$mail->send();
	}
}

$api->output(array("success" => 1));

?>
