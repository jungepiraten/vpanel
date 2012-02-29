<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class Gliederung extends StorageClass {
	private $gliederungid;
	private $label;
	private $parentid;

	private $parent;

	public static function factory(Storage $storage, $row) {
		$gliederung = new Gliederung($storage);
		$gliederung->setGliederungID($row["gliederungsid"]);
		$gliederung->setLabel($row["label"]);
		$gliederung->setParentID($row["parentid"]);
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

	public function getParentID() {
		return $this->parentid;
	}

	public function setParentID($parentid) {
		if ($this->parentid != $parentid) {
			$this->parent = null;
		}
		$this->parentid = $parentid;
	}

	public function getParent() {
		if ($this->parent == null) {
			$this->parent = $this->getStorage()->getGliederung($this->parentid);
		}
		return $this->parent;
	}

	public function setParent($parent) {
		$this->setParentID($parent->getGliederungID());
		$this->parent = $parent;
	}

	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$this->setGliederungID( $storage->setGliederung(
			$this->getGliederungID(),
			$this->getLabel(),
			$this->getParentID() ));
	}
}

?>
