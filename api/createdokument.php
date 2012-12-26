<?php

require_once(dirname(__FILE__) . "/../config.inc.php");

$session = $config->getSession(true);
$api = $session->getTemplate();

if (!$session->isSignedIn()) {
	$api->output(array("failed" => "AUTH_MISSING"), 401);
	exit;
}

$dokumenttemplate = $session->getStorage()->getDokumentTemplate($session->getVariable("dokumenttemplateid"));

if ($dokumenttemplate == null) {
	$api->output(array("failed" => "DOKUMENTTEMPLATE_MISSING"), 400);
	exit;
}

$gliederungid = $dokumenttemplate->getDokumentGliederungID($session);
$kategorieid = $dokumenttemplate->getDokumentKategorieID($session);
$statusid = $dokumenttemplate->getDokumentStatusID($session);

$file = $dokumenttemplate->getDokumentFile($session);

if (!$session->isAllowed("dokumente_create", $gliederungid)) {
	$api->output(array("failed" => "PERMISSION_DENIED"), 403);
	exit;
}

if ($file == null) {
	$api->output(array("failed" => "FILE_MISSING"), 400);
} else {
	$dokument = new Dokument($session->getStorage());
	// Zwischenspeichern um die ID zu bekommen
	$dokument->save();

	$revision = new DokumentRevision($session->getStorage());
	$revision->setDokument($dokument);
	$revision->setUser($session->getUser());
	$revision->setTimestamp(time());
	$revision->setGliederungID($gliederungid);
	$revision->setKategorieID($kategorieid);
	$revision->setStatusID($statusid);
	$revision->setIdentifier($dokumenttemplate->getDokumentIdentifier($session));
	$revision->setLabel($dokumenttemplate->getDokumentLabel($session));
	$revision->setFile($file);
	$revision->setData($dokumenttemplate->getDokumentData($session));
	$revision->setKommentar($dokumenttemplate->getDokumentKommentar($session));

	foreach ($dokumenttemplate->getDokumentFlags($session) as $flagid) {
		$flag = $session->getStorage()->getDokumentFlag($flagid);
		$revision->setFlag($flag);
	}

	$revision->save();
	$revision->notify();

	$api->output(array("success" => "1"));
}

?>
