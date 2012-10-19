<?php

require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

class DokumentMitgliederMatcher extends MitgliederMatcher {
	private $dokumentid;

	public function __construct($dokumentid) {
		$this->dokumentid = $dokumentid;
	}

	public function getDokumentID() {
		return $this->dokumentid;
	}

	public function match(Mitglied $mitglied) {
		// TODO look if this Dokument is related to this Mitglied
		return false;
	}
}

class DokumentKategorieMitgliederMatcher extends MitgliederMatcher {
	private $dokumentkategorieid;

	public function __construct($dokumentkategorieid) {
		$this->dokumentkategorieid = $dokumentkategorieid;
	}

	public function getDokumentKategorieID() {
		return $this->dokumentkategorieid;
	}

	public function match(Mitglied $mitglied) {
		// TODO look if this Mitglied is related to any Dokument in Kategorie X
		return false;
	}
}

class DokumentStatusMitgliederMatcher extends MitgliederMatcher {
	private $dokumentstatusid;

	public function __construct($dokumentstatusid) {
		$this->dokumentstatusid = $dokumentstatusid;
	}

	public function getDokumentStatusID() {
		return $this->dokumentstatusid;
	}

	public function match(Mitglied $mitglied) {
		// TODO look if this Mitglied is related to any Dokument with Status X
		return false;
	}
}

?>
