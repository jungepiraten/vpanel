<?php

require_once(VPANEL_DOKUMENTTEMPLATES . "/selectprefix.class.php");

class SelectPrefixDateDokumentTemplate extends SelectPrefixDokumentTemplate {
	private $dateFormat;
	private $dateField;

	public function __construct($templateid, $label, $permissoin, $gliederungid, $prefixes, $dateFormat, $dateField, $identifierNumberLength = 3) {
		parent::__construct($templateid, $label, $permissoin, $gliederungid, $prefixes, $identifierNumberLength);
		$this->dateFormat = $dateFormat;
		$this->dateField = $dateField;
	}

	public function getDateFieldLabel() {
		return $this->dateField;
	}

	private function getTimestamp($session) {
		return $session->getTimestampVariable("timestamp");
	}

	protected function getIdentifierPrefix($session) {
		return $this->getSelectedOption($session, "prefix") . date($this->dateFormat, $this->getTimestamp($session));
	}

	public function getDokumentLabel($session) {
		return $session->getVariable("label");
	}
}

?>
