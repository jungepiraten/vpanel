<?php

require_once(dirname(__FILE__) . "/../config.inc.php");

$session = $config->getSession(true);
$api = $session->getTemplate();

if (!$session->isSignedIn()) {
	exit;
}

$dokumenttemplate = $session->getStorage()->getDokumentTemplate($session->getVariable("dokumenttemplateid"));

if ($dokumenttemplate == null) {
	$api->output(array("failed" => "DOKUMENTTEMPLATE_MISSING"));
	exit;
}

$gliederungid = $dokumenttemplate->getDokumentGliederungID($session);
$kategorieid = $dokumenttemplate->getDokumentKategorieID($session);
$statusid = $dokumenttemplate->getDokumentStatusID($session);

$file = $dokumenttemplate->getDokumentFile($session);

if (!$session->isAllowed("dokumente_create", $gliederungid)) {
	$api->output(array("failed" => "PERMISSION_DENIED"));
	exit;
}

if ($file == null) {
	$api->output(array("failed" => "FILE_MISSING"));
} else {
	$dokument = new Dokument($session->getStorage());
	$dokument->setGliederungID($gliederungid);
	$dokument->setDokumentKategorieID($kategorieid);
	$dokument->setDokumentStatusID($statusid);
	$dokument->setIdentifier($dokumenttemplate->getDokumentIdentifier($session));
	$dokument->setLabel($dokumenttemplate->getDokumentLabel($session));
	$dokument->setFile($file);
	$dokument->setData($dokumenttemplate->getDokumentData($session));
	$dokument->save();

	$notiz = new DokumentNotiz($session->getStorage());
	$notiz->setDokument($dokument);
	$notiz->setAuthor($session->getUser());
	$notiz->setTimestamp(time());
	$notiz->setNextKategorieID($kategorieid);
	$notiz->setNextStatusID($statusid);
	$notiz->setKommentar($dokumenttemplate->getDokumentKommentar($session));
	$notiz->save();

	$notiz->notify();

	$api->output(array("success" => "1"));
}

?>
