<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

require_once(VPANEL_CORE . "/dokument.class.php");

function parseDokumentFormular($session, &$dokument = null) {
	if ($session->hasVariable("gliederungid")) {
		$gliederungid = $session->getIntVariable("gliederungid");
	}
	$kategorieid = $session->getIntVariable("kategorieid");
	$statusid = $session->getIntVariable("statusid");
	if ($session->hasFileVariable("file")) {
		$file = $session->getFileVariable("file");
	}
	if ($session->hasVariable("idkey")) {
		$idkey = $session->getVariable("idkey");
	}
	$label = $session->getVariable("label");
	$kommentar = $session->getVariable("kommentar");

	$oldkategorieid = null;
	$oldstatusid = null;
	if ($dokument == null) {
		if (!$session->isAllowed("dokumente_create", $gliederungid)) {
			$ui->viewLogin();
			exit;
		}

		$dokument = new Dokument($session->getStorage());
		$dokument->setGliederungID($gliederungid);
		$dokument->setFile($file);
		$dokument->setIdentifier($idkey);
		$dokument->setData(array());
	} else {
		$gliederungid = $dokument->getGliederungID();
		$oldkategorieid = $dokument->getDokumentKategorieID();
		$oldstatusid = $dokument->getDokumentStatusID();

		if (!$session->isAllowed("dokument_modify", $gliederungid)) {
			$ui->viewLogin();
			exit;
		}
	}
	$dokument->setDokumentKategorieID($kategorieid);
	$dokument->setDokumentStatusID($statusid);
	$dokument->setLabel($label);
	$dokument->save();

	$notiz = new DokumentNotiz($session->getStorage());
	$notiz->setDokument($dokument);
	$notiz->setAuthor($session->getUser());
	$notiz->setTimestamp(time());
	if ($oldkategorieid != $kategorieid) {
		$notiz->setNextKategorieID($kategorieid);
	}
	if ($oldstatusid != $statusid) {
		$notiz->setNextStatusID($statusid);
	}
	$notiz->setKommentar($kommentar);
	$notiz->save();

	foreach ($session->getStorage()->getDokumentNotifyList($gliederungid, $kategorieid, $statusid) as $notify) {
		$notify->notify($dokument, $notiz);
	}
}

switch ($session->hasVariable("mode") ? $session->getVariable("mode") : null) {
case "create":
	$gliederung = null;
	if ($session->hasVariable("gliederungid")) {
		$gliederung = $session->getStorage()->getGliederung($session->getVariable("gliederungid"));
	}

	$dokumentkategorie = null;
	if ($session->hasVariable("kategorieid")) {
		$dokumentkategorie = $session->getStorage()->getDokumentKategorie($session->getVariable("kategorieid"));
	}

	$dokumentstatus = null;
	if ($session->hasVariable("statusid")) {
		$dokumentstatus = $session->getStorage()->getDokumentStatus($session->getVariable("statusid"));
	}

	if ($session->getBoolVariable("save")) {
		parseDokumentFormular($session, &$dokument);

		$ui->redirect($session->getLink("dokumente_details", $dokument->getDokumentID()));
	}

	$gliederungen = $session->getStorage()->getGliederungList($session->getAllowedGliederungIDs("dokumente_create"));
	$dokumentkategorien = $session->getStorage()->getDokumentKategorieList();
	$dokumentstatuslist = $session->getStorage()->getDokumentStatusList();
	$ui->viewDokumentCreate($gliederungen, $gliederung, $dokumentkategorien, $dokumentkategorie, $dokumentstatuslist, $dokumentstatus);
	exit;
case "details":
	$dokument = $session->getStorage()->getDokument($session->getIntVariable("dokumentid"));

	if (!$session->isAllowed("dokumente_show", $dokument->getGliederungID())) {
		$ui->viewLogin();
		exit;
	}

	if ($session->getBoolVariable("save")) {
		parseDokumentFormular($session, $dokument);
		
		$ui->redirect($session->getLink("dokumente_details", $dokument->getDokumentID()));
	}
	
	$dokumentnotizen = $session->getStorage()->getDokumentNotizList($dokument->getDokumentID());
	$mitglieder = $session->getStorage()->getMitgliederByDokumentList($dokument->getDokumentID());

	$dokumentkategorien = $session->getStorage()->getDokumentKategorieList();
	$dokumentstatuslist = $session->getStorage()->getDokumentStatusList();
	$ui->viewDokumentDetails($dokument, $dokumentnotizen, $mitglieder, $dokumentkategorien, $dokumentstatuslist);
	exit;
default:
	$gliederung = null;
	if ($session->hasVariable("gliederungid")) {
		$gliederung = $session->getStorage()->getGliederung($session->getVariable("gliederungid"));
	}

	if ($session->hasVariable("kategorieid")) {
		$dokumentkategorie = $session->getStorage()->getDokumentKategorie($session->getVariable("kategorieid"));
	} else {
		$dokumentkategorie = $session->getStorage()->getDokumentKategorie($session->getDefaultDokumentKategorieID());
	}

	if ($session->hasVariable("statusid")) {
		$dokumentstatus = $session->getStorage()->getDokumentStatus($session->getVariable("statusid"));
	} else {
		$dokumentstatus = $session->getStorage()->getDokumentStatus($session->getDefaultDokumentStatusID());
	}

	$pagesize = 20;
	$pagecount = ceil($session->getStorage()->getDokumentCount($session->getAllowedGliederungIDs("dokumente_show"), $dokumentkategorie, $dokumentstatus) / $pagesize);
	$page = 0;
	if ($session->hasVariable("page") and $session->getVariable("page") >= 0 and $session->getVariable("page") < $pagecount) {
		$page = intval($session->getVariable("page"));
	}
	$offset = $page * $pagesize;

	$dokumente = $session->getStorage()->getDokumentList($session->getAllowedGliederungIDs("dokumente_show"), $gliederung, $dokumentkategorie, $dokumentstatus, $pagesize, $offset);
	$gliederungen = $session->getStorage()->getGliederungList($session->getAllowedGliederungIDs("dokumente_show"));
	$dokumentkategorien = $session->getStorage()->getDokumentKategorieList();
	$dokumentstatuslist = $session->getStorage()->getDokumentStatusList();
	$ui->viewDokumentList($dokumente, $gliederungen, $gliederung, $dokumentkategorien, $dokumentkategorie, $dokumentstatuslist, $dokumentstatus, $page, $pagecount);
	exit;
}

?>
