<?php

abstract class DokumentTemplate {
	private $templateid;
	private $label;
	private $hidden;
	private $gliederungid;

	public function __construct($templateid, $label, $hidden, $gliederungid) {
		$this->templateid = $templateid;
		$this->label = $label;
		$this->hidden = $hidden;
		$this->gliederungid = $gliederungid;
	}

	public function getDokumentTemplateID() {
		return $this->templateid;
	}

	public function getLabel() {
		return $this->label;
	}

	public function isHidden() {
		return $this->hidden;
	}

	public function getGliederungID() {
		return $this->gliederungid;
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
	abstract public function getDokumentIdentifier($session);
	abstract public function getDokumentLabel($session);
	abstract public function getDokumentFile($session);
	abstract public function getDokumentData($session);
	abstract public function getDokumentKommentar($session);
}

?>
