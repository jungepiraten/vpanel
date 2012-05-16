<?php

require_once(VPANEL_DOKUMENTTEMPLATES . "/identifier.class.php");

class DefaultDokumentTemplate extends IdentifierDokumentTemplate {
	private $kategorieid;
	private $statusid;
	private $identifierPrefix;

	public function __construct($templateid, $label, $hidden, $gliederungid, $kategorieid, $statusid, $identifierPrefix, $identifierNumberLength = 3) {
		parent::__construct($templateid, $label, $hidden, $gliederungid, $identifierNumberLength);
		$this->kategorieid = $kategorieid;
		$this->statusid = $statusid;
		$this->identifierPrefix = $identifierPrefix;
	}

	public function getDokumentKategorieID($session) {
		return $this->kategorieid;
	}

	public function getDokumentStatusID($session) {
		return $this->statusid;
	}

	protected function getIdentifierPrefix($session) {
		return $this->identifierPrefix;
	}

	public function getDokumentLabel($session) {
		return $session->getVariable("label");
	}

	public function getDokumentFile($session) {
		return $session->getFileVariable("file");
	}

	public function getDokumentData($session) {
		return array();
	}

	public function getDokumentKommentar($session) {
		return $session->getVariable("kommentar");
	}
}

?>
