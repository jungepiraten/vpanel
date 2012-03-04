<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class Beitrag extends StorageClass {
	private $beitragid;
	private $label;
	private $hoehe;

	public static function factory(Storage $storage, $row) {
		$beitrag = new Beitrag($storage);
		$beitrag->setBeitragID($row["beitragid"]);
		$beitrag->setLabel($row["label"]);
		$beitrag->setHoehe($row["hoehe"]);
		return $beitrag;
	}

	public function getBeitragID() {
		return $this->beitragid;
	}

	public function setBeitragID($beitragid) {
		$this->beitragid = $beitragid;
	}

	public function getLabel() {
		return $this->label;
	}

	public function setLabel($label) {
		$this->label = $label;
	}

	public function getHoehe() {
		return $this->hoehe;
	}

	public function setHoehe($hoehe) {
		$this->hoehe = $hoehe;
	}

	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$this->setBeitragID( $storage->setBeitrag(
			$this->getBeitragID(),
			$this->getLabel(),
			$this->getHoehe() ));
	}

	public function delete(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$storage->delMitgliederBeitragByBeitrag($this->getBeitragID());
		$storage->delBeitrag($this->getBeitragID());
	}
}

?>
