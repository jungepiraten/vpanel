<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class MitgliedBeitragBuchung extends StorageClass {
	private $buchungid;
	private $mitgliederbeitragid;
	private $gliederungid;
	private $userid;
	private $timestamp;
	private $vermerk;
	private $hoehe;

	private $mitglied;
	private $beitrag;
	private $gliederung;
	private $user;

	public static function factory(Storage $storage, $row) {
		$buchung = new MitgliedBeitragBuchung($storage);
		$buchung->setBuchungID($row["buchungid"]);
		$buchung->setMitgliederBeitragID($row["beitragid"]);
		$buchung->setGliederungID($row["gliederungid"]);
		$buchung->setUserID($row["userid"]);
		$buchung->setTimestamp($row["timestamp"]);
		$buchung->setVermerk($row["vermerk"]);
		$buchung->setHoehe($row["hoehe"]);
		return $buchung;
	}

	public function getBuchungID() {
		return $this->buchungid;
	}

	public function setBuchungID($buchungid) {
		$this->buchungid = $buchungid;
	}

	public function getMitgliederBeitragID() {
		return $this->mitgliederbeitragid;
	}

	public function setMitgliederBeitragID($mitgliederbeitragid) {
		if ($mitgliederbeitragid != $this->mitgliederbeitragid) {
			$this->mitgliederbeitrag = null;
		}
		$this->mitgliederbeitragid = $mitgliederbeitragid;
	}

	public function getMitgliederBeitrag() {
		if ($this->mitgliederbeitrag == null) {
			$this->mitgliederbeitrag = $this->getStorage()->getMitgliederBeitrag($this->mitgliederbeitragid);
		}
		return $this->mitgliederbeitrag;
	}

	public function setMitgliederBeitrag($mitgliederbeitrag) {
		$this->setMitgliederBeitragID($mitgliederbeitrag->getMitgliederBeitragID());
		$this->mitgliederbeitrag = $mitgliederbeitrag;
	}

	public function getGliederungID() {
		return $this->gliederungid;
	}

	public function setGliederungID($gliederungid) {
		if ($gliederungid != $this->gliederungid) {
			$this->gliederung = null;
		}
		$this->gliederungid = $gliederungid;
	}

	public function getGliederung() {
		if ($this->gliederung == null) {
			$this->gliederung = $this->getStorage()->getGliederung($this->gliederungid);
		}
		return $this->gliederung;
	}

	public function setGliederung($gliederung) {
		$this->setGliederungID($gliederung->getGliederungID());
		$this->gliederung = $gliederung;
	}

	public function getUserID() {
		return $this->userid;
	}

	public function setUserID($userid) {
		if ($userid != $this->userid) {
			$this->user = null;
		}
		$this->userid = $userid;
	}

	public function getUser() {
		if ($this->user == null) {
			$this->user = $this->getStorage()->getUser($this->userid);
		}
		return $this->user;
	}

	public function setUser($user) {
		$this->setUserID($user->getUserID());
		$this->user = $user;
	}
		
	public function getTimestamp() {
		return $this->timestamp;
	}

	public function setTimestamp($timestamp) {
		$this->timestamp = $timestamp;
	}

	
	public function getVermerk() {
		return $this->vermerk;
	}

	public function setVermerk($vermerk) {
		$this->vermerk = $vermerk;
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
		$this->setBuchungID( $storage->setMitgliederBeitragBuchung(
			$this->getBuchungID(),
			$this->getMitgliederBeitragID(),
			$this->getGliederungID(),
			$this->getUserID(),
			$this->getTimestamp(),
			$this->getVermerk(),
			$this->getHoehe() ));
	}

	public function delete(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$storage->delMitgliederBeitragBuchung($this->getBuchungID());
	}
}

?>
