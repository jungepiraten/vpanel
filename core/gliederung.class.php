<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class Gliederung extends StorageClass {
	private $gliederungid;
	private $label;

	public static function factory(Storage $storage, $row) {
		$gliederung = new Gliederung($storage);
		$gliederung->setGliederungID($row["gliederungsid"]);
		$gliederung->setLabel($row["label"]);
		return $gliederung;
	}

	public function getGliederungID() {
		return $this->gliederungid;
	}

	public function setGliederungID($gliederungid) {
		$this->gliederungid = $gliederungid;
	}

	public function getLabel() {
		return $this->label;
	}

	public function setLabel($label) {
		$this->label = $label;
	}

	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$this->setGliederungID( $storage->setGliederung(
			$this->getGliederungID(),
			$this->getLabel() ));
	}
}

?>
