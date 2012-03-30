<?php

require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

class AusgetretenMitgliederMatcher extends MitgliederMatcher {
	public function match(Mitglied $mitglied) {
		return $mitglied->isAusgetreten();
	}
}

class EintrittsdatumAfterMitgliederMatcher extends MitgliederMatcher {
	private $timestamp;

	public function __construct($timestamp) {
		$this->timestamp = $timestamp;
	}

	public function getTimestamp() {
		return $this->timestamp;
	}

	public function match(Mitglied $mitglied) {
		return $mitglied->getEintrittsdatum() >= $this->timestamp;
	}
}

class AustrittsdatumAfterMitgliederMatcher extends MitgliederMatcher {
	private $timestamp;

	public function __construct($timestamp) {
		$this->timestamp = $timestamp;
	}

	public function getTimestamp() {
		return $this->timestamp;
	}

	public function match(Mitglied $mitglied) {
		return $mitglied->getAustrittsdatum() >= $this->timestamp;
	}
}

?>
