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

		if (!$dokumenttemplate->isAllowed($session, $gliederungid)) {
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

	$transitionen = $session->getStorage()->getSingleDokumentTransitionList($session, $dokument);
	$dokumentkategorien = $session->getStorage()->getDokumentKategorieList();
	$dokumentstatuslist = $session->getStorage()->getDokumentStatusList();
	$mitgliedtemplates = $session->getStorage()->getMitgliederTemplateList($session);
	$ui->viewDokumentDetails($dokument, $dokumentnotizen, $mitglieder, $transitionen, $dokumentkategorien, $dokumentstatuslist, $mitgliedtemplates);
	exit;
case "transition":
	$transition = $session->getStorage()->getDokumentTransition($session->getVariable("transitionid"));

	if (!$transition->isAllowed($session)) {
		$ui->viewLogin();
		exit;
	}

	if ($session->hasVariable("dokumentid")) {
		$dokumentid = $session->getVariable("dokumentid");
		$result = $transition->execute($config, $session, $dokumentid);
	} else {
		$gliederungids = $session->getAllowedGliederungIDs($transition->getPermission());
		if ($session->hasVariable("gliederungid")) {
			$gliederungids = array_intersect($gliederungids, array($session->getIntVariable("gliederungid")));
		}
		$kategorieid = $session->getVariable("kategorieid");
		$statusid = $session->getVariable("statusid");
		$result = $transition->executeMulti($config, $session, $gliederungids, $kategorieid, $statusid);
	}
	$ui->viewDokumentTransition($transition, $result);
	exit;
case "transitionprocess":
	$transition = $session->getStorage()->getDokumentTransition($session->getVariable("transitionid"));
	$process = $session->getStorage()->getProcess($session->getVariable("processid"));

	if (!$transition->isAllowed($session)) {
		$ui->viewLogin();
		exit;
	}

	$result = $transition->show($config, $session, $process);
	$ui->viewDokumentTransitionProcess($transition, $process, $result);
	exit;
case "delete":
	$dokument = $session->getStorage()->getDokument($session->getVariable("dokumentid"));

	if (!$session->isAllowed("dokumente_delete", $dokument->getGliederungID())) {
		$ui->viewLogin();
		exit;
	}

	$dokument->delete();

	$ui->redirect();
	exit;
default:
	if ($session->hasVariable("gliederungid")) {
		$gliederung = $session->getStorage()->getGliederung($session->getVariable("gliederungid"));
	} else {
		$gliederung = $session->getStorage()->getGliederung($session->getDefaultGliederungID());
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

	$gliederungids = $session->getAllowedGliederungIDs("dokumente_show");
	if ($gliederung != null) {
		$gliederungids = array_intersect($gliederungids, array($gliederung->getGliederungID()));
	}

	$pagesize = 20;
	$pagecount = ceil($session->getStorage()->getDokumentCount($gliederungids, $dokumentkategorie, $dokumentstatus) / $pagesize);
	$page = 0;
	if ($session->hasVariable("page") and $session->getVariable("page") >= 0 and $session->getVariable("page") < $pagecount) {
		$page = intval($session->getVariable("page"));
	}
	$offset = $page * $pagesize;

	$dokumente = $session->getStorage()->getDokumentList($gliederungids, $dokumentkategorie, $dokumentstatus, $pagesize, $offset);
	$gliederungen = $session->getStorage()->getGliederungList($session->getAllowedGliederungIDs("dokumente_show"));
	$templates = $session->getStorage()->getDokumentTemplateList($session);
	$transitionen = $session->getStorage()->getMultiDokumentTransitionList($session, $dokumentkategorie, $dokumentstatus);
	$dokumentkategorien = $session->getStorage()->getDokumentKategorieList();
	$dokumentstatuslist = $session->getStorage()->getDokumentStatusList();
	$ui->viewDokumentList($dokumente, $templates, $transitionen, $gliederungen, $gliederung, $dokumentkategorien, $dokumentkategorie, $dokumentstatuslist, $dokumentstatus, $page, $pagecount);
	exit;
}

?>
