<?php

require_once(VPANEL_DOKUMENTTEMPLATES . "/defaultdate.class.php");

class RechnungDokumentTemplate extends DefaultDateDokumentTemplate {
	private $labelPrefix;
	private $knownPartner;
	private $partnerField;
	private $rechnungField;

	public function __construct($templateid, $label, $permission, $gliederungid, $kategorieid, $statusid, $flags, $identifierPrefix, $dateFormat, $dateField, $partnerField, $rechnungField, $knownPartner, $labelPrefix, $identifierNumberLength = 3) {
		parent::__construct($templateid, $label, $permission, $gliederungid, $kategorieid, $statusid, $flags, $identifierPrefix, $dateFormat, $dateField, $identifierNumberLength);
		$this->labelPrefix = $labelPrefix;
		$this->knownPartner = $knownPartner;
		$this->partnerField = $partnerField;
		$this->rechnungField = $rechnungField;
	}

	public function getDokumentLabel($session) {
		return $this->labelPrefix . " " . $session->getVariable("partner") . " " . $session->getVariable("rechnung");
	}

	public function getKnownPartner() {
		return $this->knownPartner;
	}

	public function getPartnerField() {
		return $this->partnerField;
	}

	public function getRechnungField() {
		return $this->rechnungField;
	}
}

?>
