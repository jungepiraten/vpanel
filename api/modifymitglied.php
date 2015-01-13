<?php

require_once(dirname(__FILE__) . "/../config.inc.php");

$session = $config->getSession(true);
$api = $session->getTemplate();

if (!$session->isSignedIn()) {
	$api->output(array("failed" => "AUTH_MISSING"), 401);
	exit;
}

$mitglied = $session->getStorage()->getMitglied($session->getVariable("mitgliedid"));

if ($mitglied == null) {
	$api->output(array("failed" => "MITGLIED_MISSING"), 400);
	exit;
}

if (!$session->isAllowed("mitglieder_modify", $mitglied->getLatestRevision()->getGliederungID())) {
	$api->output(array("failed" => "PERMISSION_DENIED"), 403);
	exit;
}

if (!$session->hasVariable("changes")) {
	$api->output(array("failed" => "CHANGES_MISSING"), 400);
	exit;
}

$revision = $mitglied->getLatestRevision()->fork();
$revision->setTimestamp(time());
$revision->setKommentar($session->getVariable("kommentar"));
$revision->setUser($session->getUser());

// Read Defaultvalues. Keep in mind to apply the new Values below!
$mitgliedValues = array(
	"eintritt"		=> date("Y-m-d", $mitglied->getEintrittsdatum()),
	"mitgliedschaftid"	=> $revision->getMitgliedschaftID(),
	"gliederungid"		=> $revision->getGliederungID(),
	"beitrag"		=> $revision->getBeitrag(),
	"beitragtimeformatid"	=> $revision->getBeitragTimeFormatID(),
	"adresszusatz"		=> $revision->getKontakt()->getAdresszusatz(),
	"strasse"		=> $revision->getKontakt()->getStrasse(),
	"hausnummer"		=> $revision->getKontakt()->getHausnummer(),
	"plz"			=> $revision->getKontakt()->getOrt()->getPLZ(),
	"ort"			=> $revision->getKontakt()->getOrt()->getLabel(),
	"stateid"		=> $revision->getKontakt()->getOrt()->getStateID(),
	"telefon"		=> $revision->getKontakt()->getTelefonnummer(),
	"handy"			=> $revision->getKontakt()->getHandynummer(),
	"email"			=> $revision->getKontakt()->getEMail()->getEMail(),
	"gpgfingerprint"	=> $revision->getKontakt()->getEMail()->getGPGFingerprint(),
	"austritt"		=> null,
	"typ"			=> null,
	"anrede"		=> null,
	"name"			=> null,
	"vorname"		=> null,
	"geburtsdatum"		=> null,
	"nationalitaet"		=> null,
	"kontoinhaber"		=> null,
	"iban"			=> null,
	"bic"			=> null,
);

if ($mitglied->isAusgetreten()) {
	$mitgliedValues["austritt"] = date("Y-m-d", $mitglied->getAustrittsdatum());
}

if ($revision->isNatPerson()) {
	$natperson = $revision->getNatPerson();
	$mitgliedValues = array_merge($mitgliedValues, array(
		"typ"		=> "nat",
		"anrede"	=> $natperson->getAnrede(),
		"name"		=> $natperson->getName(),
		"vorname"	=> $natperson->getVorname(),
		"geburtsdatum"	=> date("Y-m-d", $natperson->getGeburtsdatum()),
		"nationalitaet"	=> $natperson->getNationalitaet(),
	));
}

if ($revision->isJurPerson()) {
	$jurperson = $revision->getJurPerson();
	$mitgliedValues = array_merge($mitgliedValues, array(
		"typ"		=> "jur",
		"firma"		=> $jurperson->getLabel(),
	));
}

if ($revision->getKontakt()->hasKonto()) {
	$konto = $revision->getKontakt()->getKonto();
	$mitgliedValues = array_merge($mitgliedValues, array(
		"kontoinhaber"	=> $konto->getInhaber(),
		"iban"		=> $konto->getIBan(),
		"bic"		=> $konto->getBIC(),
	));
}

// Change some values
foreach ($session->getListVariable("changes") as $changeVar => $changeValue) {
	if ($changeVar == "flags") {
		foreach ($changeValue as $flagid => $flag) {
			if ($flag) {
				$revision->setFlag($session->getStorage()->getMitgliedFlag($flagid));
			} else {
				$revision->delFlag($flagid);
			}
		}
	} else {
		if (!array_key_exists($changeVar, $mitgliedValues)) {
			$api->output(array("failed" => "INVALID_CHANGE", "variable" => $changeVar), 400);
			exit;
		}

		$mitgliedValues[$changeVar] = $changeValue;
	}
}

// Save new Values
$mitglied->setEintrittsdatum(strtotime($mitgliedValues["eintritt"]));
$mitglied->setAustrittsdatum(isset($mitgliedValues["austritt"]) ? strtotime($mitgliedValues["austritt"]) : null);

$revision->setMitgliedschaftID($mitgliedValues["mitgliedschaftid"]);
$revision->setGliederungID($mitgliedValues["gliederungid"]);
$revision->isGeloescht(isset($mitgliedValues["austritt"]));
$revision->setBeitrag($mitgliedValues["beitrag"]);
$revision->setBeitragTimeFormatID($mitgliedValues["beitragtimeformatid"]);
$revision->setNatPerson($mitgliedValues["typ"] != "nat" ? null : $session->getStorage()->searchNatPerson(
	$mitgliedValues["anrede"],
	$mitgliedValues["name"],
	$mitgliedValues["vorname"],
	strtotime($mitgliedValues["geburtsdatum"]),
	$mitgliedValues["nationalitaet"]
));
$revision->setJurPerson($mitgliedValues["typ"] != "jur" ? null : $session->getStorage()->searchJurPerson(
	$mitgliedValues["firma"]
));
$revision->setKontakt($session->getStorage()->searchKontakt(
	$mitgliedValues["adresszusatz"],
	$mitgliedValues["strasse"],
	$mitgliedValues["hausnummer"],
	$session->getStorage()->searchOrt(
		$mitgliedValues["plz"],
		$mitgliedValues["ort"],
		$mitgliedValues["stateid"]
	)->getOrtID(),
	$mitgliedValues["telefon"],
	$mitgliedValues["handy"],
	$session->getStorage()->searchEMail(
		$mitgliedValues["email"]
	)->getEMailID(),
	empty($mitgliedValues["iban"]) ? null : $session->getStorage()->searchKonto(
		$mitgliedValues["kontoinhaber"],
		$mitgliedValues["iban"],
		$mitgliedValues["bic"]
	)->getKontoID()
));

$revision->save();
$mitglied->save();

$api->output(array("success" => 1));

?>
