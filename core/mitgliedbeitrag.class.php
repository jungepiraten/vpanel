<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class MitgliedBeitrag extends StorageClass {
	private $mitgliedid;
	private $beitragid;
	private $hoehe;
	private $bezahlt;

	private $mitglied;
	private $beitrag;

	public static function factory(Storage $storage, $row) {
		$mitgliedbeitrag = new MitgliedBeitrag($storage);
		$mitgliedbeitrag->setMitgliedID($row["mitgliedid"]);
		$mitgliedbeitrag->setBeitragID($row["beitragid"]);
		$mitgliedbeitrag->setHoehe($row["hoehe"]);
		$mitgliedbeitrag->setBezahlt($row["bezahlt"]);
		return $mitgliedbeitrag;
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

	public function getBezahlt() {
		return $this->bezahlt;
	}

	public function setBezahlt($bezahlt) {
		$this->bezahlt = $bezahlt;
	}
}

?>
