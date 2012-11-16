<?php

require_once(VPANEL_CORE . "/aktion.class.php");

abstract class DokumentTemplate extends GliederungAktion {
	private $templateid;

	public function __construct($templateid, $label, $permission, $gliederungid) {
		parent::__construct($label, $permission, $gliederungid);
		$this->templateid = $templateid;
	}

	public function getDokumentTemplateID() {
		return $this->templateid;
	}

	protected function getDokumentPrototype($session) {
		$dokument = new Dokument($session->getStorage());
		$dokument->setGliederungID($this->getGliederungID());
		return $dokument;
	}

	public function getDokumentGliederungID($session) {
		return $this->getGliederungID();
	}

	abstract public function getDokumentKategorieID($session);
	abstract public function getDokumentStatusID($session);
	abstract public function getDokumentFlags($session);
	abstract public function getDokumentIdentifier($session);
	abstract public function getDokumentLabel($session);
	abstract public function getDokumentFile($session);
	abstract public function getDokumentData($session);
	abstract public function getDokumentKommentar($session);
}

?>
