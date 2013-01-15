<?php

require_once(VPANEL_DOKUMENTTEMPLATES . "/default.class.php");

class DefaultDateDokumentTemplate extends DefaultDokumentTemplate {
	private $dateFormat;
	private $dateField;

	public function __construct($templateid, $label, $permission, $gliederungid, $kategorieid, $statusid, $flags, $identifierPrefix, $dateFormat, $dateField, $identifierNumberLength = 3) {
		parent::__construct($templateid, $label, $permission, $gliederungid, $kategorieid, $statusid, $flags, $identifierPrefix, $identifierNumberLength);
		$this->dateFormat = $dateFormat;
		$this->dateField = $dateField;
	}

	public function getDateFieldLabel() {
		return $this->dateField;
	}

	private function getTimestamp($session) {
		if ($session->hasVariable("timestamp")) {
			return $session->getTimestampVariable("timestamp");
		} else {
			return time();
		}
	}

	protected function getIdentifierPrefix($session) {
		return parent::getIdentifierPrefix($session) . date($this->dateFormat, $this->getTimestamp($session));
	}
}

?>
