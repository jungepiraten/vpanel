<?php

require_once(VPANEL_CORE . "/dokumentfilter.class.php");

class DokumentDokumentMatcher extends DokumentMatcher {
	private $dokumentid;

	public function __construct($dokumentid) {
		if ($dokumentid instanceof Dokument) {
			$dokumentid = $dokumentid->getDokumentID();
		}
		$this->dokumentid = $dokumentid;
	}

	public function getDokumentID() {
		return $this->dokumentid;
	}

	public function match(Dokument $dokument) {
		return $dokument->getDokumentID() == $this->getDokumentID();
	}
}

?>
