<?php

require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

class RevisionTextFieldMitgliederMatcher extends MitgliederMatcher {
	private $textfieldid;
	private $value;

	public function __construct($textfieldid, $value) {
		$this->textfieldid = $textfieldid;
		$this->value = $value;
	}

	public function getTextFieldID() {
		return $this->textfieldid;
	}

	public function getValue() {
		return $this->value;
	}

	public function match(Mitglied $mitglied) {
		if (! $mitglied->getLatestRevision()->hasTextField($this->textfieldid)) {
			return false;
		}
		return $mitglied->getLatestRevision()->getTextField($this->textfieldid)->getValue() == $this->value;
	}
}

?>
