<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

if (!$session->isAllowed("beitraege_show")) {
	$ui->viewLogin();
	exit;
}

require_once(VPANEL_CORE . "/beitrag.class.php");

function parseBeitragFormular($session, &$beitrag = null) {
	$label = $session->getVariable("label");
	$hoehe = $session->getDoubleVariable("hoehe");

	if ($beitrag == null) {
		$beitrag = new Beitrag($session->getStorage());
	}
	$beitrag->setLabel($label);
	$beitrag->setHoehe($hoehe);
	$beitrag->save();
}

switch ($session->hasVariable("mode") ? $session->getVariable("mode") : null) {
case "details":
	$beitrag = $session->getStorage()->getBeitrag($session->getIntVariable("beitragid"));

	if ($session->getBoolVariable("save")) {
		if (!$session->isAllowed("beitraege_modify")) {
			$ui->viewLogin();
			exit;
		}
		
		parseBeitragFormular($session, $beitrag);
	}

	$pagesize = 20;
	$pagecount = ceil($session->getStorage()->getMitgliederBeitragByBeitragCount($beitrag->getBeitragID()) / $pagesize);
	$page = 0;
	if ($session->hasVariable("page") and $session->getVariable("page") >= 0 and $session->getVariable("page") < $pagecount) {
		$page = intval($session->getVariable("page"));
	}
	$offset = $page * $pagesize;
	$mitgliederbeitraglist = $session->getStorage()->getMitgliederBeitragByBeitragList($beitrag->getBeitragID(), $pagesize, $offset);

	$ui->viewBeitragDetails($beitrag, $mitgliederbeitraglist, $page, $pagecount);
	exit;
case "create":
	if ($session->getBoolVariable("save")) {
		if (!$session->isAllowed("beitraege_create")) {
			$ui->viewLogin();
			exit;
		}
		
		parseBeitragFormular($session, $beitrag);
		
		$ui->redirect($session->getLink("beitraege_details", $beitrag->getBeitragID()));
	}

	$ui->viewBeitragCreate();
	exit;
case "delete":
	if (!$session->isAllowed("beitraege_delete")) {
		$ui->viewLogin();
		exit;
	}
	$beitragid = $session->getIntVariable("beitragid");
	$session->getStorage()->delMitgliederBeitragByBeitrag($beitragid);
	$session->getStorage()->delBeitrag($beitragid);
	$ui->redirect($session->getLink("beitraege"));
	exit;
default:
	$beitraege = $session->getStorage()->getBeitragList();

	$ui->viewBeitragList($beitraege);
	exit;
}

?>
