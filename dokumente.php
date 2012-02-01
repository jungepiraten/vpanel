<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

if (!$session->isAllowed("dokumente_show")) {
	$ui->viewLogin();
	exit;
}

switch ($session->hasVariable("mode") ? $session->getVariable("mode") : null) {
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
