<?php

require_once(VPANEL_CORE . "/dokumentfilter.class.php");

class MitgliedDokumentMatcher extends DokumentMatcher {
	private $mitgliedid;

	public function __construct($mitglied) {
		if ($mitglied instanceof Mitglied) {
			$mitglied = $mitglied->getMitgliedID();
		}
		$this->mitgliedid = $mitglied;
	}

	public function getMitgliedID() {
		return $this->mitgliedid;
	}

	public function match(Dokument $dokument) {
		// TODO implementieren
		return false;
	}
}

?>
