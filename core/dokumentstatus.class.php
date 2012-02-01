<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class DokumentStatus extends StorageClass {
	private $dokumentstatusid;
	private $label;

	public static function factory(Storage $storage, $row) {
		$dokumentstatus = new DokumentStatus($storage);
		$dokumentstatus->setDokumentStatusID($row["dokumentstatusid"]);
		$dokumentstatus->setLabel($row["label"]);
		return $dokumentstatus;
	}

	public function getDokumentStatusID() {
		return $this->dokumentstatusid;
	}

	public function setDokumentStatusID($dokumentstatusid) {
		$this->dokumentstatusid = $dokumentstatusid;
	}

	public function getLabel() {
		return $this->label;
	}

	public function setLabel($label) {
		$this->label = $label;
	}

	public function save(Storage $storage = null) {
		if ($storage === null) {
			$storage = $this->getStorage();
		}
		$this->setDokumentStatusID( $storage->setDokumentStatus(
			$this->getDokumentStatusID(),
			$this->getLabel() ));
	}
}

?>
