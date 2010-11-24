<?php

require_once(VPANEL_CORE . "/person.class.php");
require_once(VPANEL_CORE . "/ort.class.php");

class NatPerson extends Person {
	private $natpersonid;
	private $vorname;
	private $name;
	private $geburtsdatum;
	private $nationalitaet;
	
	public static function factory(Storage $storage, $row) {
		$natperson = new NatPerson($storage);
		$natperson->setNatPersonID($row["natpersonid"]);
		$natperson->setVorname($row["vorname"]);
		$natperson->setName($row["name"]);
		$natperson->setGeburtsdatum($row["geburtsdatum"]);
		$natperson->setNationalitaet($row["nationalitaet"]);
		return $natperson;
	}

	public function getNatPersonID() {
		return $this->natpersonid;
	}

	public function setNatPersonID($natpersonid) {
		$this->natpersonid = $natpersonid;
	}
	
	public function getVorname() {
		return $this->vorname;
	}

	public function setVorname($vorname) {
		$this->vorname = $vorname;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getGeburtsdatum() {
		return $this->geburtsdatum;
	}

	public function setGeburtsdatum($geburtsdatum) {
		$this->geburtsdatum = $geburtsdatum;
	}

	public function getNationalitaet() {
		return $this->nationalitaet;
	}

	public function setNationalitaet($nationalitaet) {
		$this->nationalitaet = $nationalitaet;
	}

	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$this->setNatPersonID( $storage->setNatPerson(
			$this->getNatPersonID(),
			$this->getName(),
			$this->getVorname(),
			$this->getGeburtsdatum(),
			$this->getNationalitaet() ));
	}
}

?>
