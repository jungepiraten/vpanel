<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

if (!$session->isAllowed("mitglieder_show")) {
	$ui->viewLogin();
	exit;
}

require_once(VPANEL_CORE . "/mitglied.class.php");
require_once(VPANEL_CORE . "/mitgliedrevision.class.php");

function parseMitgliederFormular($session, &$mitglied = null) {
	$persontyp = $session->getVariable("persontyp");
	$name = $session->getVariable("name");
	$vorname = $session->getVariable("vorname");
	$geburtsdatum = strtotime($session->getVariable("geburtsdatum"));
	$nationalitaet = $session->getVariable("nationalitaet");
	$firma = $session->getVariable("firma");
	$strasse = $session->getVariable("strasse");
	$hausnummer = $session->getVariable("hausnummer");
	$ortid = is_numeric($_POST["ortid"]) ? $_POST["ortid"] : null;
	$plz = $session->getVariable("plz");
	$ortname = $session->getVariable("ort");
	$stateid = is_numeric($_POST["stateid"]) ? $_POST["stateid"] : null;
	$telefon = $session->getVariable("telefon");
	$handy = $session->getVariable("handy");
	$email = $session->getVariable("email");
	// $gliederungid = intval($_POST["gliederungid"]);
	$gliederungid = 1;
	$gliederung = $session->getStorage()->getGliederung($gliederungid);
	$mitgliedschaftid = $session->getIntVariable("mitgliedschaftid");
	$mitgliedschaft = $session->getStorage()->getMitgliedschaft($mitgliedschaftid);
	$mitgliedpiraten = $session->getVariable("mitglied_piraten");
	$verteilereingetragen = $session->getVariable("verteiler_eingetragen");
	$beitrag = $session->getDoubleVariable("beitrag");

	$natperson = null;
	$jurperson = null;
	if ($persontyp == "nat") {
		$natperson = $session->getStorage()->searchNatPerson($name, $vorname, $geburtsdatum, $nationalitaet);
	} else {
		$jurperson = $session->getStorage()->searchJurPerson($firma);
	}
	if (is_numeric($ortid)) {
		$ort = $session->getStorage()->getOrt($ortid);
	}

	if ($ort == null) {
		$ort = $session->getStorage()->searchOrt($plz, $ortname, $stateid);
	}

	$kontakt = $session->getStorage()->searchKontakt($strasse, $hausnummer, $ort->getOrtID(), $telefon, $handy, $email);

	if ($mitglied == null) {
		$mitglied = new Mitglied($session->getStorage());
		$mitglied->setEintrittsdatum(time());
		$mitglied->setAustrittsdatum(null);
		$mitglied->save();
	}

	$revision = new MitgliedRevision($session->getStorage());
	$revision->setTimestamp(time());
	$revision->setUser($session->getUser());
	$revision->setMitglied($mitglied);
	$revision->setMitgliedschaft($mitgliedschaft);
	$revision->setGliederung($gliederung);
	$revision->isMitgliedPiraten($mitgliedpiraten);
	$revision->isVerteilerEingetragen($verteilereingetragen);
	$revision->setBeitrag($beitrag);
	$revision->setNatPerson($natperson);
	$revision->setJurPerson($jurperson);
	$revision->setKontakt($kontakt);
	$revision->save();
}

switch ($session->hasVariable("mode") ? $session->getVariable("mode") : null) {
case "details":
	$mitgliedid = intval($session->getVariable("mitgliedid"));
	$mitglied = $session->getStorage()->getMitglied($mitgliedid);

	if ($session->getBoolVariable("save")) {
		if (!$session->isAllowed("mitglieder_modify")) {
			$ui->viewLogin();
			exit;
		}

		parseMitgliederFormular($session, $mitglied);

		$ui->redirect($session->getLink("mitglieder_details", $mitglied->getMitgliedID()));
	}


	$mitgliedschaften = $session->getStorage()->getMitgliedschaftList();
	$states = $session->getStorage()->getStateList();

	$ui->viewMitgliedDetails($mitglied, $mitgliedschaften, $states);
	exit;
case "create":
	$mitgliedschaftid = intval($session->getVariable("mitgliedschaftid"));
	$mitgliedschaft = $session->getStorage()->getMitgliedschaft($mitgliedschaftid);

	if ($session->getBoolVariable("save")) {
		if (!$session->isAllowed("mitglieder_create")) {
			$ui->viewLogin();
			exit;
		}

		parseMitgliederFormular($session, &$mitglied);

		$ui->redirect($session->getLink("mitglieder_details", $mitglied->getMitgliedID()));
	}

	$mitgliedschaften = $session->getStorage()->getMitgliedschaftList();
	$states = $session->getStorage()->getStateList();

	$ui->viewMitgliedCreate($mitgliedschaft, $mitgliedschaften, $states);
	exit;
case "delete":
	if (!$session->isAllowed("mitglieder_delete")) {
		$ui->viewLogin();
		exit;
	}
	$mitgliedid = $session->getIntVariable("mitgliedid");
	$mitglied = $session->getStorage()->getMitglied($mitgliedid);
	$mitglied->setAustrittsdatum(time());
	$mitglied->save();

	$revision = $mitglied->getLatestRevision()->fork();
	$revision->setTimestamp(time());
	$revision->setUser($session->getUser());
	$revision->isGeloescht(true);
	$revision->save();

	$ui->redirect($session->getLink("mitglieder"));
	exit;
case "sendmail":
case "sendmail.select":
	$filters = $config->getMitgliederFilterList();
	$templates = $session->getStorage()->getMailTemplateList();

	$ui->viewMitgliederSendMailForm($filters, $templates);
	exit;
case "sendmail.preview":
	$filter = null;
	if ($session->hasVariable("filterid") && $config->hasMitgliederFilter($session->getVariable("filterid"))) {
		$filter = $config->getMitgliederFilter($session->getVariable("filterid"));
	}
	$mailtemplate = $session->getStorage()->getMailTemplate($session->getVariable("mailtemplateid"));

	$mitgliedercount = $session->getStorage()->getMitgliederCount($filter);
	$mitglied = array_shift($session->getStorage()->getMitgliederList($filter, 1, rand(0,$mitgliedercount-1)));

var_dump($mitglied);
	$mail = $mailtemplate->generateMail($mitglied);
var_dump($mail);

	$ui->viewMitgliederSendMailPreview($filter, $mailtemplate);
	exit;
case "sendmail.send":
	$filter = null;
	if ($session->hasVariable("filterid") && $config->hasMitgliederFilter($session->getVariable("filterid"))) {
		$filter = $config->getMitgliederFilter($session->getVariable("filterid"));
	}
	$mailtemplate = $session->getStorage()->getMailTemplate($session->getVariable("mailtemplateid"));

	$mitglieder = $session->getStorage()->getMitglieder($filter);
	
	// TODO zu einem "taskmanager" deligieren?
	// hier zu arbeiten wäre fies - das werden bis zu 10000 mails auf einmal und bricht auf jeden fall gegen die max_execution_time
	
	exit;
default:
	$filter = null;
	if ($session->hasVariable("filterid") && $config->hasMitgliederFilter($session->getVariable("filterid"))) {
		$filter = $config->getMitgliederFilter($session->getVariable("filterid"));
	}
	
	$pagesize = 20;
	$pagecount = ceil($session->getStorage()->getMitgliederCount($filter) / $pagesize);
	$page = 0;
	if ($session->hasVariable("page") and $session->getVariable("page") >= 0 and $session->getVariable("page") < $pagecount) {
		$page = intval($session->getVariable("page"));
	}
	$offset = $page * $pagesize;

	$mitglieder = $session->getStorage()->getMitgliederList($filter, $pagesize, $offset);
	$mitgliedschaften = $session->getStorage()->getMitgliedschaftList();
	$filters = $config->getMitgliederFilterList();
	$ui->viewMitgliederList($mitglieder, $mitgliedschaften, $filters, $filter, $page, $pagecount);
	exit;
}

?>
