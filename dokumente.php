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
require_once(VPANEL_MITGLIEDERMATCHER . "/dokument.class.php");
require_once(VPANEL_DOKUMENTMATCHER . "/dokument.class.php");
require_once(VPANEL_DOKUMENTMATCHER . "/logic.class.php");
require_once(VPANEL_DOKUMENTMATCHER . "/gliederung.class.php");
require_once(VPANEL_DOKUMENTMATCHER . "/kategorie.class.php");
require_once(VPANEL_DOKUMENTMATCHER . "/status.class.php");
require_once(VPANEL_DOKUMENTMATCHER . "/search.class.php");

function parseDokumentFormular($ui, $session, &$dokument = null) {
	$gliederungid = $dokument->getLatestRevision()->getGliederungID();
	$fileid = $dokument->getLatestRevision()->getFileID();
	$data = $dokument->getLatestRevision()->getData();
	$content = $dokument->getLatestRevision()->getContent();
	$kategorieid = $session->getIntVariable("kategorieid");
	$statusid = $session->getIntVariable("statusid");
	$flagids = array_keys($session->getListVariable("flags"));
	$label = $session->getVariable("label");
	$identifier = $session->getVariable("identifier");
	$kommentar = $session->getVariable("kommentar");

	if (!$session->isAllowed("dokumente_modify", $gliederungid)) {
		$ui->viewLogin();
		exit;
	}

	$revision = new DokumentRevision($session->getStorage());
	$revision->setDokument($dokument);
	$revision->setUser($session->getUser());
	$revision->setTimestamp(time());
	$revision->setGliederungID($gliederungid);
	$revision->setKategorieID($kategorieid);
	$revision->setStatusID($statusid);
	$revision->setIdentifier($identifier);
	$revision->setLabel($label);
	$revision->setFileID($fileid);
	foreach ($flagids as $flagid) {
		$flag = $session->getStorage()->getDokumentFlag($flagid);
		$revision->setFlag($flag);
	}
	$revision->setContent($content);
	$revision->setData($data);
	$revision->setKommentar($kommentar);

	$revision->save();
	$revision->notify();
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
			// Zwischenspeichern um an die ID zu kommen
			$dokument->save();

			$revision = new DokumentRevision($session->getStorage());
			$revision->setDokument($dokument);
			$revision->setUser($session->getUser());
			$revision->setTimestamp(time());
			$revision->setGliederungID($gliederungid);
			$revision->setKategorieID($kategorieid);
			$revision->setStatusID($statusid);
			$revision->setLabel($dokumenttemplate->getDokumentLabel($session));
			$revision->setIdentifier($dokumenttemplate->getDokumentIdentifier($session));
			$revision->setFile($file);
			$revision->setData($dokumenttemplate->getDokumentData($session));
			$revision->setKommentar($dokumenttemplate->getDokumentKommentar($session));

			foreach ($dokumenttemplate->getDokumentFlags($session) as $flagid) {
				$flag = $session->getStorage()->getDokumentFlag($flagid);
				$revision->setFlag($flag);
			}

			$revision->save();
			$revision->notify();

			$ui->redirect($session->getLink("dokumente_details", $dokument->getDokumentID()));
		}
	}

	$ui->viewDokumentCreate($dokumenttemplate);
	exit;
case "details":
	$dokument = $session->getStorage()->getDokument($session->getIntVariable("dokumentid"));

	if ($dokument === null) {
		$ui->redirect();
		exit;
	}

	if (!$session->isAllowed("dokumente_show", $dokument->getLatestRevision()->getGliederungID())) {
		$ui->viewLogin();
		exit;
	}

	if ($session->getBoolVariable("save")) {
		parseDokumentFormular($ui, $session, $dokument);

		$ui->redirect($session->getLink("dokumente_details", $dokument->getDokumentID()));
	}

	$dokumentrevisionen = $session->getStorage()->getDokumentRevisionList($dokument->getDokumentID());
	$mitglieder = $session->getStorage()->getMitgliederList(new DokumentMitgliederMatcher($dokument->getDokumentID()));

	$transitionen = $session->getStorage()->getSingleDokumentTransitionList($session, $dokument);
	$dokumentkategorien = $session->getStorage()->getDokumentKategorieList();
	$dokumentstatuslist = $session->getStorage()->getDokumentStatusList();
	$flags = $session->getStorage()->getDokumentFlagList();
	$mitgliedtemplates = $session->getStorage()->getMitgliederTemplateList($session);
	$ui->viewDokumentDetails($dokument, $dokumentrevisionen, $mitglieder, $transitionen, $dokumentkategorien, $dokumentstatuslist, $flags, $mitgliedtemplates);
	exit;
case "transition":
	$transition = $session->getStorage()->getDokumentTransition($session->getVariable("transitionid"));

	if (!$transition->isAllowed($session)) {
		$ui->viewLogin();
		exit;
	}

	$filter = $session->getDokumentFilter($session->getVariable("filterid"));
	$matcher = new AndDokumentMatcher(new GliederungDokumentMatcher($session->getAllowedGliederungIDs($transition->getPermission())), ($filter == null ? null : $filter->getMatcher()));

	$result = $transition->execute($config, $session, $filter, $matcher);
	$ui->viewDokumentTransition($transition, $filter, $matcher, $result);
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

	if (!$session->isAllowed("dokumente_delete", $dokument->getLatestRevision()->getGliederungID())) {
		$ui->viewLogin();
		exit;
	}

	$dokument->delete();

	$ui->redirect($session->getLink("dokumente"));
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

	$matcher = new AndDokumentMatcher(
		new GliederungDokumentMatcher($gliederungids),
		($dokumentkategorie == null ? new TrueDokumentMatcher() : new KategorieDokumentMatcher($dokumentkategorie)),
		($dokumentstatus == null ? new TrueDokumentMatcher() : new StatusDokumentMatcher($dokumentstatus)),
		($session->hasVariable("dokumentsuche") ? new SearchDokumentMatcher($session->getVariable("dokumentsuche")) : new TrueDokumentMatcher()) );
	$filter = $session->addDokumentMatcher($matcher);

	$dokumentcount = $session->getStorage()->getDokumentCount($matcher);
	$pagesize = 20;
	$pagecount = ceil($dokumentcount / $pagesize);
	$page = 0;
	if ($session->hasVariable("page") and $session->getVariable("page") >= 0 and $session->getVariable("page") < $pagecount) {
		$page = intval($session->getVariable("page"));
	}
	$offset = $page * $pagesize;

	$dokumente = $session->getStorage()->getDokumentList($matcher, $pagesize, $offset);
	$gliederungen = $session->getStorage()->getGliederungList($session->getAllowedGliederungIDs("dokumente_show"));
	$templates = $session->getStorage()->getDokumentTemplateList($session);
	$transitionen = $session->getStorage()->getMultiDokumentTransitionList($session, $dokumentkategorie, $dokumentstatus);
	$dokumentkategorien = $session->getStorage()->getDokumentKategorieList();
	$dokumentstatuslist = $session->getStorage()->getDokumentStatusList();
	$ui->viewDokumentList($dokumente, $templates, $transitionen, $gliederungen, $filter, $gliederung, $dokumentkategorien, $dokumentkategorie, $dokumentstatuslist, $dokumentstatus, $page, $pagecount);
	exit;
}

?>
