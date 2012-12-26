<?php

require_once(VPANEL_CORE . "/dokumentfilter.class.php");

class GliederungDokumentMatcher extends DokumentMatcher {
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

	public function match(Dokument $dokument) {
		return in_array($dokument->getLatestRevision()->getGliederungID(), $this->gliederungids);
	}
}

?>
