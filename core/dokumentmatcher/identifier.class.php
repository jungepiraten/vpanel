<?php

require_once(VPANEL_CORE . "/dokumentfilter.class.php");

class IdentifierDokumentMatcher extends DokumentMatcher {
	private $identifier;

	public function __construct($identifier) {
		$this->identifier = $identifier;
	}

	public function getIdentifier() {
		return $this->identifier;
	}

	public function match(Dokument $dokument) {
		return $dokument->getLatestRevision()->getIdentifier() == $this->getIdentifier();
	}
}

class IdentifierParentDokumentMatcher extends IdentifierDokumentMatcher {
	public function match(Dokument $dokument) {
		return $dokument->getLatestRevision()->getIdentifier() == substr($this->getIdentifier(), 0, strlen($dokument->getLatestRevision()->getIdentifier()));
	}
}

?>
