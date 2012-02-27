<?php

require_once(VPANEL_CORE . "/storageobject.class.php");
require_once(VPANEL_CORE . "/state.class.php");

class Ort extends StorageClass {
	private $ortid;
	private $plz;
	private $label;
	private $stateid;

	private $state;

	public static function factory(Storage $storage, $row) {
		$ort = new Ort($storage);
		$ort->setOrtID($row["ortid"]);
		$ort->setPLZ($row["plz"]);
		$ort->setLabel($row["label"]);
		$ort->setLatitude($row["latitude"]);
		$ort->setLongitude($row["longitude"]);
		$ort->setStateID($row["stateid"]);
		return $ort;
	}

	public function getOrtID() {
		return $this->ortid;
	}

	public function setOrtID($ortid) {
		$this->ortid = $ortid;
	}

	public function getPLZ() {
		return $this->plz;
	}

	public function setPLZ($plz) {
		$this->plz = $plz;
	}

	public function getLabel() {
		return $this->label;
	}

	public function setLabel($label) {
		$this->label = $label;
	}

	public function getLatitude() {
		return $this->latitude;
	}

	public function setLatitude($latitude) {
		$this->latitude = $latitude;
	}

	public function getLongitude() {
		return $this->longitude;
	}

	public function setLongitude($longitude) {
		$this->longitude = $longitude;
	}

	public function getState() {
		if ($this->state == null) {
			$this->state = $this->getStorage()->getState($this->getStateID());
		}
		return $this->state;
	}

	public function getStateID() {
		return $this->stateid;
	}

	public function setState(State $state) {
		$this->setStateID($state->getStateID());
		$this->state = $state;
	}

	public function setStateID($stateid) {
		if ($stateid != $this->stateid) {
			$this->state = null;
		}
		$this->stateid = $stateid;
	}

	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$this->setOrtID( $storage->setOrt(
			$this->getOrtID(),
			$this->getPLZ(),
			$this->getLabel(),
			$this->getLatitude(),
			$this->getLongitude(),
			$this->getStateID() ));
	}
}

?>
