<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class MitgliedBeitrag extends StorageClass {
	private $mitgliederbeitragid;
	private $mitgliedid;
	private $beitragid;
	private $hoehe;

	private $mitglied;
	private $beitrag;

	public static function factory(Storage $storage, $row) {
		$mitgliedbeitrag = new MitgliedBeitrag($storage);
		$mitgliedbeitrag->setMitgliederBeitragID($row["mitgliederbeitragid"]);
		$mitgliedbeitrag->setMitgliedID($row["mitgliedid"]);
		$mitgliedbeitrag->setBeitragID($row["beitragid"]);
		$mitgliedbeitrag->setHoehe($row["hoehe"]);
		return $mitgliedbeitrag;
	}

	public function getMitgliederBeitragID() {
		return $this->mitgliederbeitragid;
	}

	public function setMitgliederBeitragID($mitgliederbeitragid) {
		$this->mitgliederbeitragid = $mitgliederbeitragid;
	}

	public function getMitgliedID() {
		return $this->mitgliedid;
	}

	public function setMitgliedID($mitgliedid) {
		if ($mitgliedid != $this->mitgliedid) {
			$this->mitglied = null;
		}
		$this->mitgliedid = $mitgliedid;
	}

	public function getMitglied() {
		if ($this->mitglied == null) {
			$this->mitglied = $this->getStorage()->getMitglied($this->mitgliedid);
		}
		return $this->mitglied;
	}

	public function setMitglied($mitglied) {
		$this->setMitgliedID($mitglied->getMitgliedID());
		$this->mitglied = $mitglied;
	}

	public function getBeitragID() {
		return $this->beitragid;
	}

	public function setBeitragID($beitragid) {
		if ($beitragid != $this->beitragid) {
			$this->beitrag = null;
		}
		$this->beitragid = $beitragid;
	}

	public function getBeitrag() {
		if ($this->beitrag == null) {
			$this->beitrag = $this->getStorage()->getBeitrag($this->beitragid);
		}
		return $this->beitrag;
	}

	public function setBeitrag($beitrag) {
		$this->setBeitragID($beitrag->getBeitragID());
		$this->beitrag = $beitrag;
	}

	public function getHoehe() {
		return $this->hoehe;
	}

	public function setHoehe($hoehe) {
		$this->hoehe = $hoehe;
	}

	public function getBuchungen() {
		return $this->getStorage()->getMitgliederBeitragBuchungByMitgliederBeitragList($this->getMitgliederBeitragID());
	}

	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$this->setMitgliederBeitragID( $storage->setMitgliederBeitrag(
			$this->getMitgliederBeitragID(),
			$this->getMitgliedID(),
			$this->getBeitragID(),
			$this->getHoehe() ));
	}

	public function delete(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		foreach ($this->getBuchungen() as $buchung) {
			$buchung->delete($storage);
		}
		$storage->delMitgliederBeitrag($this->getMitgliederBeitragID());
	}
}

?>
