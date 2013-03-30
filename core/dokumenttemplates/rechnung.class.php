<?php

require_once(VPANEL_DOKUMENTTEMPLATES . "/default.class.php");

class RechnungDokumentTemplate extends DefaultDokumentTemplate {
	private $labelPrefix;
	private $knownPartner;
	private $partnerField;
	private $rechnungField;

	public function __construct($templateid, $label, $permission, $gliederungid, $kategorieid, $statusid, $flags, $identifierPrefix, $partnerField, $rechnungField, $knownPartner, $labelPrefix, $identifierNumberLength = 3) {
		parent::__construct($templateid, $label, $permission, $gliederungid, $kategorieid, $statusid, $flags, $identifierPrefix, $identifierNumberLength);
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

	public function getPartnerFieldLabel() {
		return $this->partnerField;
	}

	public function getRechnungFieldLabel() {
		return $this->rechnungField;
	}
}

?>
