<?php

require_once(VPANEL_DOKUMENTTEMPLATES . "/default.class.php");

class MitgliedsantragDokumentTemplate extends DefaultDokumentTemplate {
	private $mitgliedschaftid;

	public function __construct($templateid, $label, $gliederungid, $kategorieid, $statusid, $identifierPrefix, $mitgliedschaftid = null) {
		parent::__construct($templateid, $label, $gliederungid, $kategorieid, $statusid, $identifierPrefix, 1);
		$this->mitgliedschaftid = $mitgliedschaftid;
	}

	private function getAnrede($session) {
		return $session->getVariable("anrede");
	}

	private function getVorname($session) {
		return $session->getVariable("vorname");
	}

	private function getName($session) {
		return $session->getVariable("name");
	}

	private function getGeburtsdatum($session) {
		return strtotime($session->getVariable("geburtsdatum"));
	}

	private function getNationalitaet($session) {
		return $session->getVariable("nationalitaet");
	}

	private function getAdresszusatz($session) {
		return $session->getVariable("adresszusatz");
	}

	private function getStrasse($session) {
		return $session->getVariable("strasse");
	}

	private function getHausnummer($session) {
		return $session->getVariable("hausnummer");
	}

	private function getPLZ($session) {
		return $session->getVariable("plz");
	}

	private function getOrt($session) {
		return $session->getVariable("ort");
	}

	private function getTelefonnummer($session) {
		return $session->getVariable("telefon");
	}

	private function getHandynummer($session) {
		return $session->getVariable("handy");
	}

	private function getEMailAdresse($session) {
		return $session->getVariable("email");
	}

	private function getBeitrag($session) {
		return $session->getVariable("beitrag");
	}

	private function formatIdentifierName($value) {
		return strtoupper(substr(str_replace(array('ä', 'ö', 'ü', 'ß'), array('ae', 'oe', 'ue', 'ss'), strtolower($value)), 0, 3));
	}

	protected function getIdentifierPrefix($session) {
		return parent::getIdentifierPrefix($session) . "M_" . $this->formatIdentifierName($this->getName($session)) . "_" . $this->formatIdentifierName($this->getVorname($session)) . "_" . date("Ymd", $this->getGeburtsdatum($session));
	}

	public function getDokumentLabel($session) {
		return "Mitgliedsantrag " . $this->getVorname($session) . " " . $this->getName($session);
	}

	public function getDokumentFile($session) {
		return $session->getFileVariable("file");
	}

	public function getDokumentData($session) {
		return array(	"gliederungid"		=> $this->getDokumentGliederungID($session),
				"mitgliedschaftid"	=> $this->mitgliedschaftid,
				"natperson"		=> true,
				"anrede"		=> $this->getAnrede($session),
				"vorname"		=> $this->getVorname($session),
				"name"			=> $this->getName($session),
				"nationalitaet"		=> $this->getNationalitaet($session),
				"geburtsdatum"		=> date("d.m.Y", $this->getGeburtsdatum($session)),
				"adresszusatz"		=> $this->getAdresszusatz($session),
				"strasse"		=> $this->getStrasse($session),
				"hausnummer"		=> $this->getHausnummer($session),
				"plz"			=> $this->getPLZ($session),
				"ort"			=> $this->getOrt($session),
				"telefon"		=> $this->getTelefonnummer($session),
				"handy"			=> $this->getHandynummer($session),
				"email"			=> $this->getEMailAdresse($session),
				"beitrag"		=> $this->getBeitrag($session) );
	}

	public function getDokumentKommentar($session) {
		return $session->getVariable("kommentar");
	}
}

?>
