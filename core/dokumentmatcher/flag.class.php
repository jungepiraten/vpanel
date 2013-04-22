<?php

require_once(VPANEL_CORE . "/dokumentfilter.class.php");

class RevisionFlagDokumentMatcher extends DokumentMatcher {
	private $flagid;

	public function __construct($flagid) {
		$this->flagid = $flagid;
	}

	public function getFlagID() {
		return $this->flagid;
	}

	public function match(Dokument $dokument) {
		return $dokument->getLatestRevision()->hasFlag($this->flagid);
	}
}

