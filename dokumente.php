<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

if (!$session->isAllowed("dokumente_show")) {
	$ui->viewLogin();
	exit;
}

require_once(VPANEL_CORE . "/dokument.class.php");

function parseDokumentFormular($session, &$dokument = null) {
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
		$dokument = new Dokument($session->getStorage());
		$dokument->setFile($file);
		$dokument->setIdentifier($idkey);
	} else {
		$oldkategorieid = $dokument->getDokumentKategorieID();
		$oldstatusid = $dokument->getDokumentStatusID();
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
}

switch ($session->hasVariable("mode") ? $session->getVariable("mode") : null) {
case "create":
	$dokumentkategorie = null;
	if ($session->hasVariable("kategorieid")) {
		$dokumentkategorie = $session->getStorage()->getDokumentKategorie($session->getVariable("kategorieid"));
	}

	$dokumentstatus = null;
	if ($session->hasVariable("statusid")) {
		$dokumentstatus = $session->getStorage()->getDokumentStatus($session->getVariable("statusid"));
	}

	if ($session->getBoolVariable("save")) {
		if (!$session->isAllowed("dokumente_create")) {
			$ui->viewLogin();
			exit;
		}

		parseDokumentFormular($session, &$dokument);

		$ui->redirect($session->getLink("dokumente_details", $dokument->getDokumentID()));
	}

	$dokumentkategorien = $session->getStorage()->getDokumentKategorieList();
	$dokumentstatuslist = $session->getStorage()->getDokumentStatusList();
	$ui->viewDokumentCreate($dokumentkategorien, $dokumentkategorie, $dokumentstatuslist, $dokumentstatus);
	exit;
case "details":
	$dokumentid = $session->getIntVariable("dokumentid");
	$dokument = $session->getStorage()->getDokument($dokumentid);

	if ($session->getBoolVariable("save")) {
		if (!$session->isAllowed("dokumente_modify")) {
			$ui->viewLogin();
			exit;
		}
		
		parseDokumentFormular($session, $dokument);
		
		$ui->redirect($session->getLink("dokumente_details", $dokument->getDokumentID()));
	}
	
	$dokumentnotizen = $session->getStorage()->getDokumentNotizList($dokument->getDokumentID());
	$dokumentkategorien = $session->getStorage()->getDokumentKategorieList();
	$dokumentstatuslist = $session->getStorage()->getDokumentStatusList();
	$ui->viewDokumentDetails($dokument, $dokumentnotizen, $dokumentkategorien, $dokumentstatuslist);
	exit;
default:
	$dokumentkategorie = null;
	if ($session->hasVariable("kategorieid")) {
		$dokumentkategorie = $session->getStorage()->getDokumentKategorie($session->getVariable("kategorieid"));
	}

	$dokumentstatus = null;
	if ($session->hasVariable("statusid")) {
		$dokumentstatus = $session->getStorage()->getDokumentStatus($session->getVariable("statusid"));
	}

	$pagesize = 20;
	$pagecount = ceil($session->getStorage()->getDokumentCount($dokumentkategorie, $dokumentstatus) / $pagesize);
	$page = 0;
	if ($session->hasVariable("page") and $session->getVariable("page") >= 0 and $session->getVariable("page") < $pagecount) {
		$page = intval($session->getVariable("page"));
	}
	$offset = $page * $pagesize;

	$dokumente = $session->getStorage()->getDokumentList($dokumentkategorie, $dokumentstatus, $pagesize, $offset);
	$dokumentkategorien = $session->getStorage()->getDokumentKategorieList();
	$dokumentstatuslist = $session->getStorage()->getDokumentStatusList();
	$ui->viewDokumentList($dokumente, $dokumentkategorien, $dokumentkategorie, $dokumentstatuslist, $dokumentstatus, $page, $pagecount);
	exit;
}

?>
