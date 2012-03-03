<?php

require_once(VPANEL_CORE . "/dokumenttemplate.class.php");

class DefaultDokumentTemplate extends DokumentTemplate {
	private $kategorieid;
	private $statusid;
	private $identifierPrefix;
	private $identifierNumberLength;

	public function __construct($templateid, $label, $gliederungid, $kategorieid, $statusid, $identifierPrefix, $identifierNumberLength = 3) {
		parent::__construct($templateid, $label, $gliederungid);
		$this->kategorieid = $kategorieid;
		$this->statusid = $statusid;
		$this->identifierPrefix = $identifierPrefix;
		$this->identifierNumberLength = $identifierNumberLength;
	}

	public function getDokumentKategorieID($session) {
		return $this->kategorieid;
	}

	public function getDokumentStatusID($session) {
		return $this->statusid;
	}

	public function getDokumentIdentifier($session) {
		$i = "";
		do {
			$number = $session->getStorage()->getDokumentIdentifierMaxNumber($this->identifierPrefix . $i, $this->identifierNumberLength) + 1;
			$i = intval($i) + 1;
		} while (strlen($number) > $this->identifierNumberLength);
		return $this->identifierPrefix . $i . str_pad($number, $this->identifierNumberLength, "0", STR_PAD_LEFT);
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
