<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

if (!$session->isSignedIn()) {
	$ui->viewLogin();
	exit;
}

require_once(VPANEL_CORE . "/dokument.class.php");

function parseDokumentFormular($ui, $session, &$dokument = null) {
	$kategorieid = $session->getIntVariable("kategorieid");
	$statusid = $session->getIntVariable("statusid");
	$label = $session->getVariable("label");
	$kommentar = $session->getVariable("kommentar");

	$gliederungid = $dokument->getGliederungID();
	$oldkategorieid = $dokument->getDokumentKategorieID();
	$oldstatusid = $dokument->getDokumentStatusID();

	if (!$session->isAllowed("dokumente_modify", $gliederungid)) {
		$ui->viewLogin();
		exit;
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

	$notiz->notify();
}

switch ($session->hasVariable("mode") ? $session->getVariable("mode") : null) {
case "create":
	$dokumenttemplate = $session->getStorage()->getDokumentTemplate($session->getVariable("dokumenttemplateid"));

	if ($session->getBoolVariable("save")) {
		$gliederungid = $dokumenttemplate->getDokumentGliederungID($session);
		$kategorieid = $dokumenttemplate->getDokumentKategorieID($session);
		$statusid = $dokumenttemplate->getDokumentStatusID($session);

		$file = $dokumenttemplate->getDokumentFile($session);

		if (!$session->isAllowed("dokumente_create", $gliederungid)) {
			$ui->viewLogin();
			exit;
		}

		if ($file != null) {
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

			$ui->redirect($session->getLink("dokumente_details", $dokument->getDokumentID()));
		}
	}

	$gliederungen = $session->getStorage()->getGliederungList($session->getAllowedGliederungIDs("dokumente_create"));
	$dokumentkategorien = $session->getStorage()->getDokumentKategorieList();
	$dokumentstatuslist = $session->getStorage()->getDokumentStatusList();
	$ui->viewDokumentCreate($dokumenttemplate, $gliederungen, $dokumentkategorien, $dokumentstatuslist);
	exit;
case "details":
	$dokument = $session->getStorage()->getDokument($session->getIntVariable("dokumentid"));

	if (!$session->isAllowed("dokumente_show", $dokument->getGliederungID())) {
		$ui->viewLogin();
		exit;
	}

	if ($session->getBoolVariable("save")) {
		parseDokumentFormular($ui, $session, $dokument);
		
		$ui->redirect($session->getLink("dokumente_details", $dokument->getDokumentID()));
	}
	
	$dokumentnotizen = $session->getStorage()->getDokumentNotizList($dokument->getDokumentID());
	$mitglieder = $session->getStorage()->getMitgliederByDokumentList($dokument->getDokumentID());

	$dokumentkategorien = $session->getStorage()->getDokumentKategorieList();
	$dokumentstatuslist = $session->getStorage()->getDokumentStatusList();
	$mitgliedtemplates = $session->getStorage()->getMitgliedTemplateList($session->getAllowedGliederungIDs("mitglieder_create"));
	$ui->viewDokumentDetails($dokument, $dokumentnotizen, $mitglieder, $dokumentkategorien, $dokumentstatuslist, $mitgliedtemplates);
	exit;
case "delete":
	if (!$session->isAllowed("dokumente_delete")) {
		$ui->viewLogin();
		exit;
	}

	$dokument = $session->getStorage()->getDokument($session->getVariable("dokumentid"));
	$dokument->delete();

	$ui->redirect();
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
	$pagecount = ceil($session->getStorage()->getDokumentCount($session->getAllowedGliederungIDs("dokumente_show"), $gliederung, $dokumentkategorie, $dokumentstatus) / $pagesize);
	$page = 0;
	if ($session->hasVariable("page") and $session->getVariable("page") >= 0 and $session->getVariable("page") < $pagecount) {
		$page = intval($session->getVariable("page"));
	}
	$offset = $page * $pagesize;

	$dokumente = $session->getStorage()->getDokumentList($session->getAllowedGliederungIDs("dokumente_show"), $gliederung, $dokumentkategorie, $dokumentstatus, $pagesize, $offset);
	$gliederungen = $session->getStorage()->getGliederungList($session->getAllowedGliederungIDs("dokumente_show"));
	$templates = $session->getStorage()->getDokumentTemplateList($session->getAllowedGliederungIDs("dokumente_create"));
	$dokumentkategorien = $session->getStorage()->getDokumentKategorieList();
	$dokumentstatuslist = $session->getStorage()->getDokumentStatusList();
	$ui->viewDokumentList($dokumente, $templates, $gliederungen, $gliederung, $dokumentkategorien, $dokumentkategorie, $dokumentstatuslist, $dokumentstatus, $page, $pagecount);
	exit;
}

?>
