<?php

require_once(VPANEL_DOKUMENTTEMPLATES . "/identifier.class.php");

class DefaultDokumentTemplate extends IdentifierDokumentTemplate {
	private $kategorieid;
	private $statusid;
	private $flags;
	private $identifierPrefix;

	public function __construct($templateid, $label, $permission, $gliederungid, $kategorieid, $statusid, $flags, $identifierPrefix, $identifierNumberLength = 3) {
		parent::__construct($templateid, $label, $permission, $gliederungid, $identifierNumberLength);
		$this->kategorieid = $kategorieid;
		$this->statusid = $statusid;
		$this->flags = $flags;
		$this->identifierPrefix = $identifierPrefix;
	}

	public function getDokumentKategorieID($session) {
		return $this->kategorieid;
	}

	public function getDokumentStatusID($session) {
		return $this->statusid;
	}

	public function getDokumentFlags($session) {
		return $this->flags;
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
		if ($session->hasVariable("data")) {
			return $session->getVariable("data");
		}
		return array();
	}

	public function getDokumentKommentar($session) {
		return $session->getVariable("kommentar");
	}
}

?>
