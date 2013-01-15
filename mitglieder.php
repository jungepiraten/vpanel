<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

if (!$session->isSignedIn()) {
	$ui->viewLogin();
	exit;
}

require_once(VPANEL_CORE . "/mitglied.class.php");
require_once(VPANEL_CORE . "/mitgliedrevision.class.php");
require_once(VPANEL_CORE . "/mitgliedbeitrag.class.php");
require_once(VPANEL_CORE . "/mitgliedbeitragbuchung.class.php");
require_once(VPANEL_CORE . "/mitgliederfilter.class.php");
require_once(VPANEL_DOKUMENTMATCHER . "/mitglied.class.php");
require_once(VPANEL_MITGLIEDERMATCHER . "/logic.class.php");
require_once(VPANEL_MITGLIEDERMATCHER . "/gliederung.class.php");
require_once(VPANEL_MITGLIEDERMATCHER . "/flag.class.php");
require_once(VPANEL_MITGLIEDERMATCHER . "/ausgetreten.class.php");
require_once(VPANEL_MITGLIEDERMATCHER . "/natperson-age.class.php");
require_once(VPANEL_MITGLIEDERMATCHER . "/search.class.php");
require_once(VPANEL_MITGLIEDERMATCHER . "/ort.class.php");

function parseMitgliederFormular($ui, $session, &$mitglied = null, $dokument = null) {
	global $config;

	$eintritt = $session->getTimestampVariable("eintritt");
	$austritt = $session->getTimestampVariable("austritt");
	$persontyp = $session->getVariable("persontyp");
	$anrede = $session->getVariable("anrede");
	$name = $session->getVariable("name");
	$vorname = $session->getVariable("vorname");
	$geburtsdatum = $session->getTimestampVariable("geburtsdatum");
	$nationalitaet = $session->getVariable("nationalitaet");
	$firma = $session->getVariable("firma");
	$adresszusatz = $session->getVariable("adresszusatz");
	$strasse = $session->getVariable("strasse");
	$hausnummer = $session->getVariable("hausnummer");
	$plz = $session->getVariable("plz");
	$ortname = $session->getVariable("ort");
	$stateid = is_numeric($session->getVariable("stateid")) ? $session->getVariable("stateid") : null;
	$telefon = $session->getVariable("telefon");
	$handy = $session->getVariable("handy");
	$email = $session->getVariable("email");
	$iban = $session->getVariable("iban");
	$iban = empty($iban) ? null : $iban;
	$gliederungid = intval($session->getVariable("gliederungid"));
	$gliederung = $session->getStorage()->getGliederung($gliederungid);
	$mitgliedschaftid = $session->getIntVariable("mitgliedschaftid");
	$mitgliedschaft = $session->getStorage()->getMitgliedschaft($mitgliedschaftid);
	$beitrag = $session->getDoubleVariable("beitrag");
	$beitragtimeformat = $session->getStorage()->getBeitragTimeFormat($session->getIntVariable("beitragtimeformatid"));
	$flags = $session->getListVariable("flags");
	$textfields = $session->getListVariable("textfields");
	$kommentar = $session->getVariable("kommentar");

	$natperson = null;
	$jurperson = null;
	if ($persontyp == "nat") {
		$natperson = $session->getStorage()->searchNatPerson($anrede, $name, $vorname, $geburtsdatum, $nationalitaet);
	} else {
		$jurperson = $session->getStorage()->searchJurPerson($firma);
	}

	$ort = $session->getStorage()->searchOrt($plz, $ortname, $stateid);
	$email = $session->getStorage()->searchEMail($email);
	$kontakt = $session->getStorage()->searchKontakt($adresszusatz, $strasse, $hausnummer, $ort->getOrtID(), $telefon, $handy, $email->getEMailID(), $iban);

	if ($mitglied == null) {
		if (!$session->isAllowed("mitglieder_create", $gliederung->getGliederungID())) {
			$ui->viewLogin();
			exit;
		}

		$mitglied = new Mitglied($session->getStorage());
		$mitglied->setGlobalID($config->generateGlobalID());
		// Zwischenspeichern, um ersten Beitrag hinzuzufuegen
		$mitglied->save();

		$beitragobj = $beitragtimeformat->getBeitrag();
		if ($beitragobj != null) {
			$mitgliedbeitrag = new MitgliedBeitrag($session->getStorage());
			$mitgliedbeitrag->setMitglied($mitglied);
			$mitgliedbeitrag->setBeitrag($beitragobj);
			$mitgliedbeitrag->setHoehe($beitragobj->getHoehe() == null ? $beitrag : $beitragobj->getHoehe());
			$mitgliedbeitrag->save();
		}
	} else {
		if (!$session->isAllowed("mitglieder_modify", $mitglied->getLatestRevision()->getGliederungID())) {
			$ui->viewLogin();
			exit;
		}
		if ($gliederung->getGliederungID() != $mitglied->getLatestRevision()->getGliederungID()
		 && !$session->isAllowed("mitglieder_moveto", $gliederung->getGliederungID())) {
			$ui->viewLogin();
			exit;
		}
	}

	$mitglied->setEintrittsdatum($eintritt);
	$mitglied->setAustrittsdatum($austritt);
	$mitglied->save();

	$revision = new MitgliedRevision($session->getStorage());
	$revision->setTimestamp(time());
	$revision->setGlobalID($config->generateGlobalID());
	$revision->setUser($session->getUser());
	$revision->setMitglied($mitglied);
	$revision->setMitgliedschaft($mitgliedschaft);
	$revision->setGliederung($gliederung);
	$revision->setBeitrag($beitrag);
	$revision->setBeitragTimeFormat($beitragtimeformat);
	$revision->setNatPerson($natperson);
	$revision->setJurPerson($jurperson);
	$revision->setKontakt($kontakt);
	$revision->setKommentar($kommentar);
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

function parseMitgliederBeitraegeFormular($ui, $session, &$mitglied) {
	$beitraege_hoehe = $session->getListVariable("beitraege_hoehe");

	if (!$session->isAllowed("mitglieder_modify", $mitglied->getLatestRevision()->getGliederungID())) {
		$ui->viewLogin();
		exit;
	}

	foreach ($beitraege_hoehe as $beitragid => $hoehe) {
		$mitglied->getBeitrag($beitragid)->setHoehe($hoehe);
	}

	$mitglied->save();
}

switch ($session->hasVariable("mode") ? $session->getVariable("mode") : null) {
case "stats":
	if (!$session->isAllowed("mitglieder_show")) {
		$ui->viewLogin();
		exit;
	}

	$process = $session->getStorage()->getProcess($session->getVariable("processid"));
	if ($process instanceof MitgliederFilterStatsProcess) {
		$ui->viewMitgliederStats($process);
	}
	break;

case "beitraege":
	$mitgliedid = intval($session->getVariable("mitgliedid"));
	$mitglied = $session->getStorage()->getMitglied($mitgliedid);

	if (!$session->isAllowed("mitglieder_modify", $mitglied->getLatestRevision()->getGliederungID())) {
		$ui->viewLogin();
		exit;
	}

	if ($session->getBoolVariable("save")) {
		parseMitgliederBeitraegeFormular($ui, $session, $mitglied);
	}

	if ($session->getBoolVariable("neu")) {
		$beitraege_neu_beitragid = $session->getVariable("beitrag_neu_beitragid");
		$beitraege_neu_hoehe = $session->getDoubleVariable("beitrag_neu_hoehe");
		$beitrag = $session->getStorage()->getBeitrag($beitraege_neu_beitragid);

		if ($beitrag != null) {
			if ($beitraege_neu_hoehe == null) {
				$beitraege_neu_hoehe = $beitrag->getHoehe();
			}
			if ($beitraege_neu_hoehe == null) {
				$beitraege_neu_hoehe = $mitglied->getLatestRevision()->getBeitrag();
			}
			if ($beitraege_neu_hoehe != null) {
				$beitrag = $mitglied->getBeitrag($beitraege_neu_beitragid);
				$beitrag->setHoehe($beitraege_neu_hoehe);
				$beitrag->save();
			}
		}
	}

	$ui->redirect();
	break;

case "beitragdelete":
	$beitrag = $session->getStorage()->getMitgliederBeitrag($session->getIntVariable("mitgliedbeitragid"));

	if (!$session->isAllowed("mitglieder_modify", $beitrag->getMitglied()->getLatestRevision()->getGliederungID())) {
		$ui->viewLogin();
		exit;
	}

	$beitrag->delete();

	$ui->redirect();
	break;

case "beitraege_buchungen":
	$beitrag = $session->getStorage()->getMitgliederBeitrag($session->getIntVariable("mitgliedbeitragid"));
	$buchungen = $beitrag->getBuchungen();

	if ($session->getBoolVariable("add")) {
		$timestamp = $session->getTimestampVariable("timestamp");
		$gliederung = $session->getStorage()->getGliederung($session->getIntVariable("gliederungid"));
		$vermerk = $session->getVariable("vermerk");
		$hoehe = $session->getDoubleVariable("hoehe");

		if ($gliederung != null) {
			if (!$session->isAllowed("mitglieder_modify", $beitrag->getMitglied()->getLatestRevision()->getGliederungID())) {
				$ui->viewLogin();
				exit;
			}

			$buchung = new MitgliedBeitragBuchung($session->getStorage());
			$buchung->setMitgliederBeitrag($beitrag);
			$buchung->setGliederung($gliederung);
			$buchung->setUser($session->getUser());
			$buchung->setTimestamp($timestamp);
			$buchung->setVermerk($vermerk);
			$buchung->setHoehe($hoehe);
			$buchung->save();

			if ($session->hasVariable("mailtemplateid")) {
				$mailtemplate = $session->getStorage()->getMailTemplate($session->getVariable("mailtemplateid"));
				if ($mailtemplate != null) {
					$mail = $mailtemplate->generateMail($beitrag->getMitglied());
					$config->getSendMailBackend()->send($mail);
				}
			}
			$ui->redirect();
		}
	}

	$ui->redirect();
	break;

case "beitraege_buchungen_delete":
	$buchung = $session->getStorage()->getMitgliederBeitragBuchung($session->getIntVariable("buchungid"));

	if (!$session->isAllowed("mitglieder_show", $buchung->getMitgliederBeitrag()->getMitglied()->getLatestRevision()->getGliederungID())) {
		$ui->viewLogin();
		exit;
	}

	$buchung->delete();
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

	if (!$session->isAllowed("mitglieder_show", $revision->getGliederungID())) {
		$ui->viewLogin();
		exit;
	}

	$revisions = $mitglied->getRevisionList();

	if ($session->getBoolVariable("save")) {
		parseMitgliederFormular($ui, $session, $mitglied);

		$ui->redirect($session->getLink("mitglieder_details", $mitglied->getMitgliedID()));
	}

	$dokumente = $session->getStorage()->getDokumentList(new MitgliedDokumentMatcher($mitglied->getMitgliedID()));

	$gliederungen = $session->getStorage()->getGliederungList($session->getAllowedGliederungIDs("mitglieder_show"));
	$mitgliedschaften = $session->getStorage()->getMitgliedschaftList();
	$beitragtimeformats = $session->getStorage()->getBeitragTimeFormatList();
	$mitgliederflags = $session->getStorage()->getMitgliedFlagList();
	$mitgliedertextfields = $session->getStorage()->getMitgliedTextFieldList();
	$mailtemplates = $session->getStorage()->getMailTemplateList($session->getAllowedGliederungIDs("mitglieder_show"));
	$filteractions = $session->getStorage()->getMitgliederFilterActionList();
	$states = $session->getStorage()->getStateList();
	$beitraege = $session->getStorage()->getBeitragList();

	$ui->viewMitgliedDetails($mitglied, $revisions, $revision, $dokumente, $gliederungen, $mitgliedschaften, $beitragtimeformats, $mailtemplates, $filteractions, $states, $mitgliederflags, $mitgliedertextfields, $beitraege);
	exit;

case "create":
	$data = array();

	$dokument = null;
	if ($session->hasVariable("dokumentid")) {
		$dokument = $session->getStorage()->getDokument($session->getVariable("dokumentid"));
		$data = $dokument->getLatestRevision()->getData();
	}

	$template = null;
	if (isset($data["mitgliedtemplateid"])) {
		$template = $session->getStorage()->getMitgliederTemplate($data["mitgliedtemplateid"]);
	}
	if ($session->hasVariable("mitgliedtemplateid")) {
		$template = $session->getStorage()->getMitgliederTemplate($session->getVariable("mitgliedtemplateid"));
	}

	if ($session->getBoolVariable("save")) {
		parseMitgliederFormular($ui, $session, &$mitglied, $dokument);

		if ($dokument != null) {
			$ui->redirect($session->getLink("dokumente_details", $dokument->getDokumentID()));
		} else {
			$ui->redirect($session->getLink("mitglieder_details", $mitglied->getMitgliedID()));
		}
	}

	$gliederungen = $session->getStorage()->getGliederungList($session->getAllowedGliederungIDs("mitglieder_create"));
	$mitgliedschaften = $session->getStorage()->getMitgliedschaftList();
	$beitragtimeformats = $session->getStorage()->getBeitragTimeFormatList();
	$mailtemplates = $session->getStorage()->getMailTemplateList($session->getAllowedGliederungIDs("mitglieder_create"));
	$mitgliederflags = $session->getStorage()->getMitgliedFlagList();
	$mitgliedertextfields = $session->getStorage()->getMitgliedTextFieldList();
	$states = $session->getStorage()->getStateList();

	$ui->viewMitgliedCreate($template, $dokument, $data, $gliederungen, $mitgliedschaften, $beitragtimeformats, $mailtemplates, $states, $mitgliederflags, $mitgliedertextfields);
	exit;

case "filteraction":
	$filteraction = $session->getStorage()->getMitgliederFilterAction($session->getVariable("actionid"));

	if (!$session->isAllowed($filteraction->getPermission())) {
		$ui->viewLogin();
		exit;
	}

	$filter = $session->getMitgliederFilter($session->getVariable("filterid"));
	$matcher = new AndMitgliederMatcher(new GliederungMitgliederMatcher($session->getAllowedGliederungIDs($filteraction->getPermission())), ($filter == null ? null : $filter->getMatcher()));

	$result = $filteraction->execute($config, $session, $filter, $matcher);
	$ui->viewMitgliederFilterAction($filteraction, $filter, $matcher, $result);
	exit;

case "filterprocess":
	$filteraction = $session->getStorage()->getMitgliederFilterAction($session->getVariable("actionid"));
	$process = $session->getStorage()->getProcess($session->getVariable("processid"));

	if (!$session->isAllowed($filteraction->getPermission())) {
		$ui->viewLogin();
		exit;
	}

	$result = $filteraction->show($config, $session, $process);
	$ui->viewMitgliederFilterProcess($filteraction, $process, $result);
	exit;

case "composefilter":
	function buildComposedMatcher($session, $filter, $id) {
		if (!isset($filter[$id])) {
			return null;
		}
		if (isset($filter[$id]["childs"])) {
			$childs = array();
			foreach ($filter[$id]["childs"] as $childID) {
				$child = buildComposedMatcher($session, $filter, $childID);
				if ($child != null) {
					$childs[] = $child;
				}
			}
		}
		switch ($filter[$id]["type"]) {
		case "and":
			if (!isset($childs) || count($childs) == 0) {
				return null;
			}
			return new AndMitgliederMatcher($childs);
		case "or":
			if (!isset($childs) || count($childs) == 0) {
				return null;
			}
			return new OrMitgliederMatcher($childs);
		case "not":
			if (!isset($childs) || count($childs) != 1) {
				return null;
			}
			return new NotMitgliederMatcher(array_shift($childs));
		case "preset":
			if (!isset($filter[$id]["filterid"]) || !$session->hasMitgliederMatcher($filter[$id]["filterid"])) {
				return null;
			}
			return $session->getMitgliederMatcher($filter[$id]["filterid"]);
		case "flag":
			return new RevisionFlagMitgliederMatcher($filter[$id]["flagid"]);
		case "eintrittafter":
			$timestamp = strtotime($filter[$id]["timestamp"]);
			return new EintrittsdatumAfterMitgliederMatcher($timestamp);
		case "austrittafter":
			$timestamp = strtotime($filter[$id]["timestamp"]);
			return new AustrittsdatumAfterMitgliederMatcher($timestamp);
		case "age":
			$age = intval($filter[$id]["age"]);
			return new NatPersonAgeMitgliederMatcher($age);
		case "eintrittage":
			$age = intval($filter[$id]["age"]);
			return new EintrittAgeNatPersonMitgliederMatcher($age);
		case "search":
			if (empty($filter[$id]["query"])) {
				return null;
			}
			return new SearchMitgliederMatcher($filter[$id]["query"]);
		case "umkreis":
			if (!is_numeric($filter[$id]["lat"]) || !is_numeric($filter[$id]["lng"]) || !is_numeric($filter[$id]["radius"])) {
				return null;
			}
			return new OrtDistanceMitgliederMatcher($filter[$id]["lat"], $filter[$id]["lng"], $filter[$id]["radius"]);
		default:
			return null;
		}
	}

	if ($session->hasVariable("generate")) {
		$matcher = buildComposedMatcher($session, $session->getListVariable("filter"), "matcher");
		if ($matcher != null) {
			$filter = $session->addMitgliederMatcher($matcher);
			$ui->redirect($session->getLink("mitglieder_page", $filter->getFilterID(), 0));
		}
	}

	$filters = $session->getStorage()->getMitgliederFilterList($session);
	$flags = $session->getStorage()->getMitgliedFlagList();
	$ui->viewMitgliederComposeFilter($filters, $flags);
	exit;

default:
	$filter = null;

	if ($session->hasVariable("mitgliedersuche")) {
		$term = $session->getVariable("mitgliedersuche");
		if (is_numeric($term)) {
			$ui->redirect($session->getLink("mitglieder_details", $term));
		}
		$filter = $session->addMitgliederMatcher(new SearchMitgliederMatcher($term));
	}

	if ($session->hasVariable("filterid")) {
		$filter = $session->getMitgliederFilter($session->getVariable("filterid"));
	}

	$matcher = ($filter != null ? $filter->getMatcher() : null);
	$matcher = new AndMitgliederMatcher(new GliederungMitgliederMatcher($session->getAllowedGliederungIDs("mitglieder_show")), $matcher);

	$mitgliedercount = $session->getStorage()->getMitgliederCount($matcher);
	$pagesize = 20;
	$pagecount = ceil($mitgliedercount / $pagesize);
	$page = 0;
	if ($session->hasVariable("page") and $session->getVariable("page") >= 0 and $session->getVariable("page") < $pagecount) {
		$page = intval($session->getVariable("page"));
	}
	$offset = $page * $pagesize;

	$mitglieder = $session->getStorage()->getMitgliederList($matcher, $pagesize, $offset);
	$mitgliedtemplates = $session->getStorage()->getMitgliederTemplateList($session);
	$filters = $session->getStorage()->getMitgliederFilterList($session);
	$filteractions = $session->getStorage()->getMitgliederFilterActionList($session);

	$ui->viewMitgliederList($mitglieder, $mitgliedtemplates, $filteractions, $filters, $filter, $page, $pagecount, $mitgliedercount);
	exit;
}

?>
