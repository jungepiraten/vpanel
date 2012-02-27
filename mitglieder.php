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
require_once(VPANEL_CORE . "/mitgliednotiz.class.php");
require_once(VPANEL_CORE . "/mitgliedrevision.class.php");
require_once(VPANEL_CORE . "/mitgliederfilter.class.php");
require_once(VPANEL_CORE . "/tempfile.class.php");
require_once(VPANEL_CORE . "/mitgliederstatistik.class.php");
require_once(VPANEL_PROCESSES . "/mitgliederfilterdelete.class.php");
require_once(VPANEL_PROCESSES . "/mitgliederfiltersendmail.class.php");
require_once(VPANEL_PROCESSES . "/mitgliederfilterexport.class.php");
require_once(VPANEL_PROCESSES . "/mitgliederfilterstatistik.class.php");
require_once(VPANEL_PROCESSES . "/mitgliederfilterbeitrag.class.php");
require_once(VPANEL_MITGLIEDERMATCHER . "/search.class.php");

$predefinedfields = array(
	array("label" => "Bezeichnung",		"template" => "{BEZEICHNUNG}"),
	array("label" => "Anschrift",		"template" => "{STRASSE} {HAUSNUMMER}"),
	array("label" => "Adresszusatz",	"template" => "{ADRESSZUSATZ}"),
	array("label" => "PLZ",			"template" => "{PLZ}"),
	array("label" => "Ort",			"template" => "{ORT}"),
	array("label" => "Bundesland",		"template" => "{STATE}"),
	array("label" => "Telefonnummer",	"template" => "{TELEFONNUMMER}"),
	array("label" => "Handynummer",		"template" => "{HANDYNUMMER}"),
	array("label" => "E-Mail",		"template" => "{EMAIL}"),
	array("label" => "Beitrag",		"template" => "{BEITRAG}"),
	array("label" => "Mitgliedschaft",	"template" => "{MITGLIEDSCHAFT}")
	);

function parseMitgliederFormular($session, &$mitglied = null, $dokument = null) {
	global $config;

	$persontyp = $session->getVariable("persontyp");
	$anrede = $session->getVariable("anrede");
	$name = $session->getVariable("name");
	$vorname = $session->getVariable("vorname");
	$geburtsdatum = strtotime($session->getVariable("geburtsdatum"));
	$nationalitaet = $session->getVariable("nationalitaet");
	$firma = $session->getVariable("firma");
	$adresszusatz = $session->getVariable("adresszusatz");
	$strasse = $session->getVariable("strasse");
	$hausnummer = $session->getVariable("hausnummer");
	$plz = $session->getVariable("plz");
	$ortname = $session->getVariable("ort");
	$stateid = is_numeric($_POST["stateid"]) ? $_POST["stateid"] : null;
	$telefon = $session->getVariable("telefon");
	$handy = $session->getVariable("handy");
	$email = $session->getVariable("email");
	$gliederungid = intval($_POST["gliederungid"]);
	$gliederung = $session->getStorage()->getGliederung($gliederungid);
	$mitgliedschaftid = $session->getIntVariable("mitgliedschaftid");
	$mitgliedschaft = $session->getStorage()->getMitgliedschaft($mitgliedschaftid);
	$beitrag = $session->getDoubleVariable("beitrag");
	$flags = $session->getListVariable("flags");
	$textfields = $session->getListVariable("textfields");

	$natperson = null;
	$jurperson = null;
	if ($persontyp == "nat") {
		$natperson = $session->getStorage()->searchNatPerson($anrede, $name, $vorname, $geburtsdatum, $nationalitaet);
	} else {
		$jurperson = $session->getStorage()->searchJurPerson($firma);
	}

	$ort = $session->getStorage()->searchOrt($plz, $ortname, $stateid);
	$email = $session->getStorage()->searchEMail($email);
	$kontakt = $session->getStorage()->searchKontakt($adresszusatz, $strasse, $hausnummer, $ort->getOrtID(), $telefon, $handy, $email->getEMailID());

	if ($mitglied == null) {
		$mitglied = new Mitglied($session->getStorage());
		$mitglied->setGlobalID($config->generateGlobalID());
		$mitglied->setEintrittsdatum(time());
		$mitglied->setAustrittsdatum(null);
		$mitglied->save();

		$mitgliedbeitrag = new MitgliedBeitrag($session->getStorage());
		$mitgliedbeitrag->setMitglied($mitglied);
		$mitgliedbeitrag->setBeitrag($session->getStorage()->searchBeitrag(date("Y"), null));
		$mitgliedbeitrag->setHoehe($beitrag);
		$mitglied->setBeitrag($mitgliedbeitrag);
		$mitglied->save();
	}

	$revision = new MitgliedRevision($session->getStorage());
	$revision->setTimestamp(time());
	$revision->setGlobalID($config->generateGlobalID());
	$revision->setUser($session->getUser());
	$revision->setMitglied($mitglied);
	$revision->setMitgliedschaft($mitgliedschaft);
	$revision->setGliederung($gliederung);	$revision->setBeitrag($beitrag);
	$revision->setNatPerson($natperson);
	$revision->setJurPerson($jurperson);
	$revision->setKontakt($kontakt);
	foreach ($flags as $flagid => $selected) {
		$revision->setFlag($session->getStorage()->getMitgliedFlag($flagid));
	}
	foreach ($textfields as $textfieldid => $value) {
		$revision->setTextField($session->getStorage()->getMitgliedTextField($textfieldid), $value);
	}
	$revision->save();

	if ($dokument != null) {
		$session->getStorage()->addMitgliedDokument($mitglied->getMitgliedID(), $dokument->getDokumentID());
	}

	if ($session->hasVariable("mailtemplateid")) {
		$mailtemplate = $session->getStorage()->getMailTemplate($session->getVariable("mailtemplateid"));
		if ($mailtemplate != null) {
			$mail = $mailtemplate->generateMail($mitglied);
			$config->getSendMailBackend()->send($mail);
		}
	}
}

function parseAddMitgliederNotizFormular($session, $mitglied, &$notiz) {
	$kommentar = $session->getVariable("kommentar");

	if ($notiz == null) {
		$notiz = new MitgliedNotiz($session->getStorage());
	}
	$notiz->setMitglied($mitglied);
	$notiz->setAuthor($session->getUser());
	$notiz->setTimestamp(time());
	$notiz->setKommentar($kommentar);
	$notiz->save();
}

function parseMitgliederBeitraegeFormular($session, &$mitglied) {
	$beitraege_hoehe = $session->getListVariable("beitraege_hoehe");
	$beitraege_bezahlt = $session->getListVariable("beitraege_bezahlt");
	$beitraege_neu_beitragid = $session->getVariable("beitrag_neu_beitragid");
	$beitraege_neu_hoehe = $session->getDoubleVariable("beitrag_neu_hoehe");
	$beitraege_neu_bezahlt = $session->getDoubleVariable("beitrag_neu_bezahlt");

	foreach ($beitraege_hoehe as $beitragid => $hoehe) {
		$beitrag = $session->getStorage()->getBeitrag($beitragid);
		$bezahlt = $beitraege_bezahlt[$beitragid];
		$mitglied->setBeitrag($beitrag, $hoehe, $bezahlt);
	}

	if (is_numeric($beitraege_neu_beitragid)) {
		$beitrag = $session->getStorage()->getBeitrag($beitraege_neu_beitragid);
		if ($beitraege_neu_hoehe == null) {
			$beitraege_neu_hoehe = $beitrag->getHoehe();
		}
		if ($beitraege_neu_hoehe == null) {
			$beitraege_neu_hoehe = $mitglied->getLatestRevision()->getBeitrag();
		}
		$mitglied->setBeitrag($beitrag, $beitraege_neu_hoehe, $beitraege_neu_bezahlt);
	}

	$mitglied->save();
}

switch ($session->hasVariable("mode") ? $session->getVariable("mode") : null) {
case "statistik":
	$statistik = $session->getStorage()->getMitgliederStatistik($session->getVariable("statistikid"));

	$ui->viewMitgliederStatistik($statistik);
	break;
case "beitraege":
	$mitgliedid = intval($session->getVariable("mitgliedid"));
	$mitglied = $session->getStorage()->getMitglied($mitgliedid);

	if (!$session->isAllowed("mitglieder_modify")) {
		$ui->viewLogin();
		exit;
	}

	if ($session->getBoolVariable("save")) {
		parseMitgliederBeitraegeFormular($session, $mitglied);
		$ui->redirect();
	}

	break;
case "beitragdelete":
	$mitglied = $session->getStorage()->getMitglied($session->getIntVariable("mitgliedid"));
	$beitrag = $session->getStorage()->getBeitrag($session->getIntVariable("beitragid"));

	$mitglied->delBeitrag($beitrag->getBeitragID());
	$mitglied->save();
	
	$ui->redirect();
	break;
case "details":
	if ($session->hasVariable("revisionid")) {
		$revisionid = intval($session->getVariable("revisionid"));
		$revision = $session->getStorage()->getMitgliederRevision($revisionid);
		$mitglied = $revision->getMitglied();
	} else if ($session->hasVariable("mitgliedid")) {
		$mitgliedid = intval($session->getVariable("mitgliedid"));
		$mitglied = $session->getStorage()->getMitglied($mitgliedid);
		$revision = $mitglied->getLatestRevision();
	}

	$revisions = $mitglied->getRevisionList();

	if ($session->getBoolVariable("save")) {
		if (!$session->isAllowed("mitglieder_modify")) {
			$ui->viewLogin();
			exit;
		}

		parseMitgliederFormular($session, $mitglied);

		$ui->redirect($session->getLink("mitglieder_details", $mitglied->getMitgliedID()));
	}

	if ($session->getBoolVariable("addnotiz")) {
		if (!$session->isAllowed("mitglieder_modify")) {
			$ui->viewLogin();
			exit;
		}
		
		parseAddMitgliederNotizFormular($session, $mitglied, $notiz);
	}
	
	$notizen = $session->getStorage()->getMitgliedNotizList($mitglied->getMitgliedID());
	$dokumente = $session->getStorage()->getDokumentByMitgliedList($mitglied->getMitgliedID());

	$gliederungen = $session->getStorage()->getGliederungList();
	$mitgliedschaften = $session->getStorage()->getMitgliedschaftList();
	$mitgliederflags = $session->getStorage()->getMitgliedFlagList();
	$mitgliedertextfields = $session->getStorage()->getMitgliedTextFieldList();
	$states = $session->getStorage()->getStateList();
	$beitraege = $session->getStorage()->getBeitragList();

	$ui->viewMitgliedDetails($mitglied, $revisions, $revision, $notizen, $dokumente, $gliederungen, $mitgliedschaften, $states, $mitgliederflags, $mitgliedertextfields, $beitraege);
	exit;
case "create":
	$data = array();

	$dokument = null;
	if ($session->hasVariable("dokumentid")) {
		$dokument = $session->getStorage()->getDokument($session->getVariable("dokumentid"));
		$data = $dokument->getData();
	}

	$template = null;
	if (isset($data["mitgliedtemplateid"])) {
		$template = $session->getStorage()->getMitgliedTemplate($data["mitgliedtemplateid"]);
	}
	if ($session->hasVariable("mitgliedtemplateid")) {
		$template = $session->getStorage()->getMitgliedTemplate($session->getVariable("mitgliedtemplateid"));
	}

	if ($session->getBoolVariable("save")) {
		if (!$session->isAllowed("mitglieder_create")) {
			$ui->viewLogin();
			exit;
		}

		parseMitgliederFormular($session, &$mitglied, $dokument);

		if ($dokument != null) {
			$ui->redirect($session->getLink("dokumente_details", $dokument->getDokumentID()));
		} else {
			$ui->redirect($session->getLink("mitglieder_details", $mitglied->getMitgliedID()));
		}
	}

	$gliederungen = $session->getStorage()->getGliederungList();
	$mitgliedschaften = $session->getStorage()->getMitgliedschaftList();
	$mailtemplates = $session->getStorage()->getMailTemplateList();
	$mitgliederflags = $session->getStorage()->getMitgliedFlagList();
	$mitgliedertextfields = $session->getStorage()->getMitgliedTextFieldList();
	$states = $session->getStorage()->getStateList();

	$ui->viewMitgliedCreate($template, $dokument, $data, $gliederungen, $mitgliedschaften, $mailtemplates, $states, $mitgliederflags, $mitgliedertextfields);
	exit;
case "delete":
	if (!$session->isAllowed("mitglieder_delete")) {
		$ui->viewLogin();
		exit;
	}

	$matcher = $session->getMitgliederMatcher($session->getVariable("filterid"));

	$process = new MitgliederFilterDeleteProcess($session->getStorage());
	$process->setMatcher($matcher);
	$process->setUserID($session->getUser()->getUserID());
	$process->setTimestamp(time());
	$process->setFinishedPage($session->getLink("mitglieder_page", $session->getVariable("filterid"), 0));
	$process->save();

	$ui->redirect($session->getLink("processes_view", $process->getProcessID()));
	exit;
case "sendmail":
case "sendmail.select":
	$filters = $session->getStorage()->getMitgliederFilterList();
	$templates = $session->getStorage()->getMailTemplateList();

	$ui->viewMitgliederSendMailForm($filters, $templates);
	exit;
case "sendmail.preview":
	$filter = $session->getMitgliederFilter($session->getVariable("filterid"));
	$mailtemplate = $session->getStorage()->getMailTemplate($session->getVariable("mailtemplateid"));

	$mitgliedercount = $session->getStorage()->getMitgliederCount($filter);
	$mitglied = array_shift($session->getStorage()->getMitgliederList($filter, 1, rand(0,$mitgliedercount-1)));
	$mail = $mailtemplate->generateMail($mitglied);

	$ui->viewMitgliederSendMailPreview($mail, $filter, $mailtemplate);
	exit;
case "sendmail.send":
	$matcher = $session->getMitgliederMatcher($session->getVariable("filterid"));
	$mailtemplate = $session->getStorage()->getMailTemplate($session->getVariable("templateid"));
	
	$process = new MitgliederFilterSendMailProcess($session->getStorage());
	$process->setBackend($config->getSendMailBackend());
	$process->setMatcher($matcher);
	$process->setTemplate($mailtemplate);
	$process->setFinishedPage($session->getLink("mitglieder_page", $session->getVariable("filterid"), 0));
	$process->save();

	$ui->redirect($session->getLink("processes_view", $process->getProcessID()));
	exit;
case "export.options":
	$filters = $session->getStorage()->getMitgliederFilterList();
	
	$ui->viewMitgliederExportOptions($filters, $predefinedfields);
	exit;
case "export.export":
	$matcher = $session->getMitgliederMatcher($session->getVariable("filterid"));

	// Headerfelder
	$exportfields = array();
	foreach ($session->getListVariable("simpleexportfields") as $fieldid) {
		$predefinedfield = $predefinedfields[$fieldid];
		$exportfields[$predefinedfield["label"]] = $predefinedfield["template"];
	}

	$exportfieldfields = $session->getListVariable("exportfields");
	$exportfieldvalues = $session->getListVariable("exportvalues");
	$exportfields = array_merge($exportfields, array_combine($exportfieldfields, $exportfieldvalues));
	unset($exportfields[""]);

	$tempfile = new TempFile($session->getStorage());
	$tempfile->setUser($session->getUser());
	$tempfile->setTimestamp(time());
	$file = new File($session->getStorage());
	$file->setExportFilename("vpanel-export-" . date("Y-m-d"));
	$file->save();
	$tempfile->setFile($file);
	$tempfile->save();

	$process = new MitgliederFilterExportCSVProcess($session->getStorage());
	$process->setMatcher($matcher);
	$process->setFile($tempfile);
	$process->setFields($exportfields);
	$process->setFinishedPage($session->getLink("tempfile_get", $tempfile->getTempFileID()));
	$process->save();

	$ui->redirect($session->getLink("processes_view", $process->getProcessID()));
	exit;
case "statistik.start":
	$matcher = $session->getMitgliederMatcher($session->getVariable("filterid"));

	$statistik = new MitgliederStatistik($session->getStorage());
	$statistik->setTimestamp(time());

	$agegraphfile = new File($session->getStorage());
	$agegraphfile->setExportFilename("vpanel-agegraph-" . date("Y-m-d"));
	$agegraphfile->save();
	$statistik->setAgeGraphFile($agegraphfile);

	$timegraphfile = new File($session->getStorage());
	$timegraphfile->setExportFilename("vpanel-timegraph-" . date("Y-m-d"));
	$timegraphfile->save();
	$statistik->setTimeGraphFile($timegraphfile);
	$statistik->save();

	$process = new MitgliederFilterStatistikProcess($session->getStorage());
	$process->setMatcher($matcher);
	$process->setStatistik($statistik);
	$process->setFinishedPage($session->getLink("mitglieder_statistik", $statistik->getStatistikID()));
	$process->save();
	$ui->redirect($session->getLink("processes_view", $process->getProcessID()));
	exit;
case "setbeitrag.selectbeitrag":
	$filters = $session->getStorage()->getMitgliederFilterList();
	$beitraglist = $session->getStorage()->getBeitragList();

	$ui->viewMitgliederSetBeitragSelect($filters, $beitraglist);
	exit;
case "setbeitrag.start":
	$matcher = $session->getMitgliederMatcher($session->getVariable("filterid"));
	$beitrag = $session->getStorage()->getBeitrag($session->getIntVariable("beitragid"));

	$process = new MitgliederFilterBeitragProcess($session->getStorage());
	$process->setMatcher($matcher);
	$process->setBeitrag($beitrag);
	$process->setFinishedPage($ui->getRedirectURL());
	$process->save();
	$ui->redirect($session->getLink("processes_view", $process->getProcessID()));
	exit;
default:
	$filter = null;

	if ($session->hasVariable("mitgliedersuche")) {
		$filter = $session->addMitgliederMatcher(new SearchMitgliederMatcher($session->getVariable("mitgliedersuche")));
	}
	
	if ($session->hasVariable("filterid")) {
		$filter = $session->getMitgliederFilter($session->getVariable("filterid"));
	}

	$mitgliedercount = $session->getStorage()->getMitgliederCount($filter);
	$pagesize = 20;
	$pagecount = ceil($mitgliedercount / $pagesize);
	$page = 0;
	if ($session->hasVariable("page") and $session->getVariable("page") >= 0 and $session->getVariable("page") < $pagecount) {
		$page = intval($session->getVariable("page"));
	}
	$offset = $page * $pagesize;

	$mitglieder = $session->getStorage()->getMitgliederList($filter, $pagesize, $offset);
	$mitgliedtemplates = $session->getStorage()->getMitgliedTemplateList();
	$filters = $session->getStorage()->getMitgliederFilterList();
	$ui->viewMitgliederList($mitglieder, $mitgliedtemplates, $filters, $filter, $page, $pagecount, $mitgliedercount);
	exit;
}

?>
