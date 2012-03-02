<?php

require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

class GliederungMitgliederMatcher extends MitgliederMatcher {
	private $gliederungids;
	
	public function __construct($gliederung) {
		if (!is_array($gliederung)) {
			$gliederung = array($gliederung);
		}
		$this->gliederungids = array_map(array($this, "parseGliederung"), $gliederung);
	}

	private function parseGliederung($gliederung) {
		if ($gliederung instanceof Gliederung) {
			$gliederung = $gliederung->getGliederungID();
		}
		return $gliederung;
	}

	public function getGliederungIDs() {
		return $this->gliederungids;
	}

	public function match(Mitglied $mitglied) {
		return in_array($mitglied->getLatestRevision()->getGliederungID(), $this->gliederungids);
	}
}

?>
