<?php

require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

class GliederungMitgliederMatcher extends MitgliederMatcher {
	private $gliederungid;
	
	public function __construct($gliederung) {
		if ($gliederung instanceof Gliederung) {
			$this->gliederungid = $gliederung->getGliederungID();
		} else {
			$this->gliederungid = $gliederung;
		}
	}

	public function getGliederungID() {
		return $this->gliederungid;
	}

	public function match(Mitglied $mitglied) {
		return $mitglied->getLatestRevision()->getGliederungID() == $this->gliederungid;
	}
}

?>
