<?php

require_once(VPANEL_CORE . "/dokumentfilter.class.php");

class StatusDokumentMatcher extends DokumentMatcher {
	private $statusid;

	public function __construct($status) {
		if ($status instanceof DokumentStatus) {
			$status = $status->getDokumentStatusID();
		}
		$this->statusid = $status;
	}

	public function getStatusID() {
		return $this->statusid;
	}

	public function match(Dokument $dokument) {
		return $dokument->getLatestRevision()->getStatusID() == $this->getStatusID();
	}
}

?>
