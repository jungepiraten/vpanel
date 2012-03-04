<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

$mitglied = null;
if ($session->hasVariable("mitgliedid")) {
	$mitglied = $session->getStorage()->getMitglied($session->getVariable("mitgliedid"));
}

$dokument = null;
if ($session->hasVariable("dokumentid")) {
	$dokument = $session->getStorage()->getDokument($session->getVariable("dokumentid"));
}

if (isset($mitglied) && !$session->isAllowed("mitglieder_modify", $mitglied->getLatestRevision()->getGliederungID())) {
	$ui->viewLogin();
	exit;
}

if (isset($dokument) && !$session->isAllowed("dokumente_modify", $mitglied->getLatestRevision()->getGliederungID())) {
	$ui->viewLogin();
	exit;
}

if ($mitglied != null && $dokument != null) {
	switch ($session->hasVariable("mode") ? $session->getVariable("mode") : null) {
	case "add":
		$session->getStorage()->addMitgliedDokument($mitglied->getMitgliedID(), $dokument->getDokumentID());
		break;
	case "delete":
		$session->getStorage()->delMitgliedDokument($mitglied->getMitgliedID(), $dokument->getDokumentID());
		break;
	}
	
	$ui->redirect();
} else {
	$ui->viewMitgliedDokumentForm($mitglied, $dokument);
}

?>
