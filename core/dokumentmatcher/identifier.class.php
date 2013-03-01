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

?>
