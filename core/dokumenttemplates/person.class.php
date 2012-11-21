<?php

require_once(VPANEL_DOKUMENTTEMPLATES . "/default.class.php");

class NatPersonDokumentTemplate extends DefaultDokumentTemplate {
	private $labelPrefix;

	public function __construct($templateid, $label, $permission, $gliederungid, $kategorieid, $statusid, $flags, $identifierPrefix, $labelPrefix) {
		parent::__construct($templateid, $label, $permission, $gliederungid, $kategorieid, $statusid, $flags, $identifierPrefix, 1);
		$this->labelPrefix = $labelPrefix;
	}

	private function getLabelPrefix($session) {
		return $this->labelPrefix;
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
		return $session->getTimestampVariable("geburtsdatum");
	}

	private function getNationalitaet($session) {
		return $session->getVariable("nationalitaet");
	}

	private function formatIdentifierName($value) {
		return strtoupper(substr(str_replace(array('ä', 'ö', 'ü', 'ß'), array('ae', 'oe', 'ue', 'ss'), strtolower($value)), 0, 3));
	}

	protected function getIdentifierPrefix($session) {
		return parent::getIdentifierPrefix($session) . $this->formatIdentifierName($this->getName($session)) . "_" . $this->formatIdentifierName($this->getVorname($session)) . "_" . date("Ymd", $this->getGeburtsdatum($session));
	}

	public function getDokumentLabel($session) {
		return $this->getLabelPrefix($session) . " " . $this->getVorname($session) . " " . $this->getName($session);
	}

	public function getDokumentFile($session) {
		return $session->getFileVariable("file");
	}

	public function getDokumentData($session) {
		return array(	"gliederungid"		=> $this->getDokumentGliederungID($session),
				"natperson"		=> true,
				"anrede"		=> $this->getAnrede($session),
				"vorname"		=> $this->getVorname($session),
				"name"			=> $this->getName($session),
				"nationalitaet"		=> $this->getNationalitaet($session),
				"geburtsdatum"		=> date("Y-m-d", $this->getGeburtsdatum($session)) );
	}

	public function getDokumentKommentar($session) {
		return $session->getVariable("kommentar");
	}
}

?>
