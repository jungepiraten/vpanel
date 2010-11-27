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
	$persontyp = stripslashes($_POST["persontyp"]);
	$name = stripslashes($_POST["name"]);
	$vorname = stripslashes($_POST["vorname"]);
	$geburtsdatum = strtotime($_POST["geburtsdatum"]);
	$nationalitaet = stripslashes($_POST["nationalitaet"]);
	$firma = stripslashes($_POST["firma"]);
	$strasse = stripslashes($_POST["strasse"]);
	$hausnummer = stripslashes($_POST["hausnummer"]);
	$ortid = is_numeric($_POST["ortid"]) ? $_POST["ortid"] : null;
	$plz = stripslashes($_POST["plz"]);
	$ortname = stripslashes($_POST["ort"]);
	$stateid = is_numeric($_POST["stateid"]) ? $_POST["stateid"] : null;
	$telefon = stripslashes($_POST["telefon"]);
	$handy = stripslashes($_POST["handy"]);
	$email = stripslashes($_POST["email"]);
	// $gliederungid = intval($_POST["gliederungid"]);
	$gliederungid = 1;
	$gliederung = $session->getStorage()->getGliederung($gliederungid);
	$mitgliedschaftid = intval($_REQUEST["mitgliedschaftid"]);
	$mitgliedschaft = $session->getStorage()->getMitgliedschaft($mitgliedschaftid);
	$mitgliedpiraten = isset($_POST["mitglied_piraten"]);
	$verteilereingetragen = isset($_POST["verteiler_eingetragen"]);
	$beitrag = doubleval($_POST["beitrag"]);

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

switch (isset($_REQUEST["mode"]) ? stripslashes($_REQUEST["mode"]) : null) {
case "details":
	$mitgliedid = intval($_REQUEST["mitgliedid"]);
	$mitglied = $session->getStorage()->getMitglied($mitgliedid);

	if (isset($_REQUEST["save"])) {
		if (!$session->isAllowed("mitglieder_modify")) {
			$ui->viewLogin();
			exit;
		}

		parseMitgliederFormular($session, $mitglied);

		$ui->redirect($session->getLink("mitglieder_details", $mitglied->getMitgliedID()));
	}

	$mitgliedschaften = $session->getStorage()->getMitgliedschaftList();
	$orte = $session->getStorage()->getOrtList();
	$states = $session->getStorage()->getStateList();

	$ui->viewMitgliedDetails($mitglied, $mitgliedschaften, $orte, $states);
	exit;
case "create":
	$mitgliedschaftid = intval($_REQUEST["mitgliedschaftid"]);
	$mitgliedschaft = $session->getStorage()->getMitgliedschaft($mitgliedschaftid);

	if (isset($_REQUEST["save"])) {
		if (!$session->isAllowed("mitglieder_create")) {
			$ui->viewLogin();
			exit;
		}

		parseMitgliederFormular($session, &$mitglied);

		$ui->redirect($session->getLink("mitglieder_details", $mitglied->getMitgliedID()));
	}

	$mitgliedschaften = $session->getStorage()->getMitgliedschaftList();
	$orte = $session->getStorage()->getOrtList();
	$states = $session->getStorage()->getStateList();

	$ui->viewMitgliedCreate($mitgliedschaft, $mitgliedschaften, $orte, $states);
	exit;
case "delete":
	if (!$session->isAllowed("mitglieder_delete")) {
		$ui->viewLogin();
		exit;
	}
	$mitgliedid = intval($_REQUEST["mitgliedid"]);
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
default:
	$pagesize = 20;
	$pagecount = ceil($session->getStorage()->getMitgliederCount() / $pagesize);
	$page = 0;
	if (isset($_REQUEST["page"]) and $_REQUEST["page"] >= 0 and $_REQUEST["page"] < $pagecount) {
		$page = intval($_REQUEST["page"]);
	}
	$offset = $page * $pagesize;

	$mitglieder = $session->getStorage()->getMitgliederList($pagesize, $offset);
	$mitgliedschaften = $session->getStorage()->getMitgliedschaftList();
	$ui->viewMitgliederList($mitglieder, $mitgliedschaften, $page, $pagecount);
	exit;
}

?>
