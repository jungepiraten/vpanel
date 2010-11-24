<?php

require_once(VPANEL_CORE . "/person.class.php");

class JurPerson extends Person {
	private $jurpersonid;
	private $label;
	
	public static function factory(Storage $storage, $row) {
		$jurperson = new JurPerson($storage);
		$jurperson->setJurPersonID($row["jurpersonid"]);
		$jurperson->setLabel($row["label"]);
		return $jurperson;
	}

	public function getJurPersonID() {
		return $this->jurpersonid;
	}

	public function setJurPersonID($jurpersonid) {
		$this->jurpersonid = $jurpersonid;
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
		$this->setJurPersonID( $storage->setJurPerson(
			$this->getJurPersonID(),
			$this->getLabel() ));
	}
}

?>
