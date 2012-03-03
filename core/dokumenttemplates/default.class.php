<?php

require_once(VPANEL_CORE . "/dokumenttemplate.class.php");

class DefaultDokumentTemplate extends DokumentTemplate {
	private $kategorieid;
	private $statusid;
	private $identifierPrefix;

	public function __construct($templateid, $label, $gliederungid, $kategorieid, $statusid, $identifierPrefix) {
		parent::__construct($templateid, $label, $gliederungid);
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

	public function getDokumentIdentifier($session) {
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
