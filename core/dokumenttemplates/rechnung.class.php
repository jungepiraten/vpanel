<?php

require_once(VPANEL_DOKUMENTTEMPLATES . "/defaultdate.class.php");

class RechnungDokumentTemplate extends DefaultDateDokumentTemplate {
	private $knownPartner;

	public function __construct($templateid, $label, $permission, $gliederungid, $kategorieid, $statusid, $flags, $identifierPrefix, $dateFormat, $dateField, $knownPartner, $identifierNumberLength = 3) {
		parent::__construct($templateid, $label, $permission, $gliederungid, $kategorieid, $statusid, $flags, $identifierPrefix, $dateFormat, $dateField, $identifierNumberLength);
		$this->knownPartner = $knownPartner;
	}

	public function getDokumentLabel($session) {
		return "RE " . $session->getVariable("partner") . " " . $session->getVariable("rechnung");
	}

	public function getKnownPartner() {
		return $this->knownPartner;
	}
}

?>
