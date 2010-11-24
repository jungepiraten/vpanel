<?php

require_once(VPANEL_CORE . "/person.class.php");

class JurPerson extends Person {
	private $jurpersonid;
	private $firma;
	
	public static function factory(Storage $storage, $row) {
		$jurperson = new JurPerson($storage);
		$jurperson->setJurPersonID($row["jurpersonid"]);
		$jurperson->setFirma($row["label"]);
		return $jurperson;
	}

	public function getJurPersonID() {
		return $this->jurpersonid;
	}

	public function setJurPersonID($jurpersonid) {
		$this->jurpersonid = $jurpersonid;
	}

	public function getFirma() {
		return $this->firma;
	}

	public function setFirma($firma) {
		$this->firma = $firma;
	}

	public function load() {
		if ($this->jurpersonid == null) {
			$row = $this->getStorage()->getJurPerson($this->getJurPersonID());
			$this->setJurPersonID($row["jurpersonid"]);
			$this->setFirma($row["firma"]);
		}
	}

	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$storage->setJurPerson(
			$this->getJurPersonID(),
			$this->getFirma() );
	}
}

?>
