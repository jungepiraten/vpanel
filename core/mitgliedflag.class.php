<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class MitgliedFlag extends StorageClass {
	private $flagid;
	private $label;

	public static function factory(Storage $storage, $row) {
		$flag = new MitgliedFlag($storage);
		$flag->setFlagID($row["flagid"]);
		$flag->setLabel($row["label"]);
		return $flag;
	}

	public function getFlagID() {
		return $this->flagid;
	}

	public function setFlagID($flagid) {
		$this->flagid = $flagid;
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
		$this->setFlagID( $storage->setMitgliedFlag(
			$this->getFlagID(),
			$this->getLabel() ));
	}
}

?>
