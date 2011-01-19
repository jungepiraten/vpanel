<?php

require_once(VPANEL_CORE . "/globalobject.class.php");
require_once(VPANEL_CORE . "/user.class.php");
require_once(VPANEL_CORE . "/mitglied.class.php");
require_once(VPANEL_CORE . "/mitgliedschaft.class.php");
require_once(VPANEL_CORE . "/gliederung.class.php");
require_once(VPANEL_CORE . "/natperson.class.php");
require_once(VPANEL_CORE . "/jurperson.class.php");
require_once(VPANEL_CORE . "/kontakt.class.php");

class MitgliedRevision extends GlobalClass {
	private $revid;
	private $timestamp;
	private $userid;
	private $mitgliedid;
	private $mitgliedschaftid;
	private $gliederungid;
	private $geloescht;
	private $mitglied_piraten;
	private $verteiler_eingetragen;
	private $beitrag;
	private $natpersonid;
	private $jurpersonid;
	private $kontaktid;
	
	private $user;
	private $mitglied;
	private $mitgliedschaft;
	private $gliederung;
	private $natperson;
	private $jurperson;
	private $kontakt;

	public static function factory(Storage $storage, $row) {
		$revision = new MitgliedRevision($storage);
		$revision->setRevisionID($row["revisionid"]);
		$revision->setGlobalID($row["globaleid"]);
		$revision->setTimestamp($row["timestamp"]);
		$revision->setUserID($row["userid"]);
		$revision->setMitgliedID($row["mitgliedid"]);
		$revision->setMitgliedschaftID($row["mitgliedschaftid"]);
		$revision->setGliederungID($row["gliederungsid"]);
		$revision->isGeloescht($row["geloescht"]);
		$revision->isMitgliedPiraten($row["mitglied_piraten"]);
		$revision->isVerteilerEingetragen($row["verteiler_eingetragen"]);
		$revision->setBeitrag($row["beitrag"]);
		$revision->setNatPersonID($row["natpersonid"]);
		$revision->setJurPersonID($row["jurpersonid"]);
		$revision->setKontaktID($row["kontaktid"]);
		return $revision;
	}

	public function fork() {
		$r = new MitgliedRevision($this->getStorage());
		$r->setMitgliedID($this->getMitgliedID());
		$r->setMitgliedschaftID($this->getMitgliedschaftID());
		$r->setGliederungID($this->getGliederungID());
		$r->isGeloescht($this->isGeloescht());
		$r->isMitgliedPiraten($this->isMitgliedPiraten());
		$r->isVerteilerEingetragen($this->isVerteilerEingetragen());
		$r->setBeitrag($this->getBeitrag());
		$r->setNatPersonID($this->getNatPersonID());
		$r->setJurPersonID($this->getJurPersonID());
		$r->setKontaktID($this->getKontaktID());
		return $r;
	}

	public function getRevisionID() {
		return $this->revid;
	}

	public function setRevisionID($revid) {
		$this->revid = $revid;
	}

	public function getTimestamp() {
		return $this->timestamp;
	}

	public function setTimestamp($timestamp) {
		$this->timestamp = $timestamp;
	}

	public function getUser() {
		if ($this->user == null) {
			$this->user = $this->getStorage()->getUser($this->getUserID());
		}
		return $this->user;
	}

	public function getUserID() {
		return $this->userid;
	}

	public function setUser(User $user) {
		$this->setUserID($user->getUserID());
		$this->user = $user;
	}

	public function setUserID($userid) {
		if ($userid == $this->userid) {
			$this->user = null;
		}
		$this->userid = $userid;
	}

	public function getMitglied() {
		if ($this->mitglied == null) {
			$this->mitglied = Mitglied::factoryByMitgliedID($this->getStorage(), $this->getMitgliedID());
		}
		return $this->mitglied;
	}

	public function getMitgliedID() {
		return $this->mitgliedid;
	}

	public function setMitglied(Mitglied $mitglied) {
		$this->setMitgliedID($mitglied->getMitgliedID());
		$this->mitglied = $mitglied;
	}

	public function setMitgliedID($mitgliedid) {
		if ($mitgliedid != $this->mitgliedid) {
			$this->mitglied = null;
		}
		$this->mitgliedid = $mitgliedid;
	}

	public function getMitgliedschaft() {
		if ($this->mitgliedschaft == null) {
			$this->mitgliedschaft = $this->getStorage()->getMitgliedschaft($this->getMitgliedschaftID());
		}
		return $this->mitgliedschaft;
	}

	public function getMitgliedschaftID() {
		return $this->mitgliedschaftid;
	}

	public function setMitgliedschaft(Mitgliedschaft $mitgliedschaft) {
		$this->setMitgliedschaftID($mitgliedschaft->getMitgliedschaftID());
		$this->mitgliedschaft = $mitgliedschaft;
	}

	public function setMitgliedschaftID($mitgliedschaftid) {
		if ($mitgliedschaftid != $this->mitgliedschaftid) {
			$this->mitgliedschaft = null;
		}
		$this->mitgliedschaftid = $mitgliedschaftid;
	}

	public function getGliederung() {
		if ($this->gliederung == null) {
			$this->gliederung = Gliederung::factoryByGliederungID($this->getStorage(), $this->getGliederungID());
		}
		return $this->gliederung;
	}

	public function getGliederungID() {
		return $this->gliederungid;
	}

	public function setGliederung(Gliederung $gliederung) {
		$this->setGliederungID($gliederung->getGliederungID());
		$this->gliederung = $gliederung;
	}

	public function setGliederungID($gliederungid) {
		if ($gliederungid != $this->gliederungid) {
			$this->gliederung = null;
		}
		$this->gliederungid = $gliederungid;
	}

	public function isGeloescht($geloescht = null) {
		if ($geloescht !== null) {
			$this->geloescht = $geloescht == true;
		}
		return $this->geloescht;
	}

	public function isMitgliedPiraten($mitgliedpiraten = null) {
		if ($mitgliedpiraten !== null) {
			$this->mitglied_piraten = $mitgliedpiraten == true;
		}
		return $this->mitglied_piraten;
	}

	public function isVerteilerEingetragen($verteilereingetragen = null) {
		if ($verteilereingetragen !== null) {
			$this->verteiler_eingetragen = $verteilereingetragen == true;
		}
		return $this->verteiler_eingetragen;
	}

	public function getBeitrag() {
		return $this->beitrag;
	}

	public function setBeitrag($beitrag) {
		$this->beitrag = $beitrag;
	}

	public function getKontakt() {
		if ($this->kontakt == null) {
			$this->kontakt = $this->getStorage()->getKontakt($this->getKontaktID());
		}
		return $this->kontakt;
	}

	public function getKontaktID() {
		return $this->kontaktid;
	}

	public function setKontakt(Kontakt $kontakt) {
		$this->setKontaktID($kontakt->getKontaktID());
		$this->kontakt = $kontakt;
	}

	public function setKontaktID($kontaktid) {
		if ($this->kontaktid != $kontaktid) {
			$this->kontakt = null;
		}
		$this->kontaktid = $kontaktid;
	}

	public function isNatPerson() {
		return $this->getNatPersonID() != null;
	}

	public function setNatPerson($natperson) {
		$this->setNatPersonID($natperson == null ? null : $natperson->getNatPersonID());
		$this->natperson = $natperson;
	}

	public function setNatPersonID($natpersonid) {
		if ($natpersonid != $this->natpersonid) {
			$this->natpersonid = null;
		}
		$this->natpersonid = $natpersonid;
	}

	public function getNatPerson() {
		if ($this->natperson == null) {
			$this->natperson = $this->getStorage()->getNatPerson($this->getNatPersonID());
		}
		return $this->natperson;
	}

	public function getNatPersonID() {
		return $this->natpersonid;
	}

	public function isJurPerson() {
		return $this->getJurPersonID() != null;
	}

	public function setJurPerson($jurperson) {
		$this->setJurPersonID($jurperson == null ? null : $jurperson->getJurPersonID());
		$this->jurperson = $jurperson;
	}

	public function setJurPersonID($jurpersonid) {
		if ($jurpersonid != $this->jurpersonid) {
			$this->jurperson = null;
		}
		$this->jurpersonid = $jurpersonid;
	}

	public function getJurPerson() {
		if ($this->jurperson == null) {
			$this->jurperson = $this->getStorage()->getJurPerson($this->getJurPersonID());
		}
		return $this->jurperson;
	}

	public function getJurPersonID() {
		return $this->jurpersonid;
	}

	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$this->setRevisionID( $storage->setMitgliederRevision(
			$this->getRevisionID(),
			$this->getGlobalID(),
			$this->getTimestamp(),
			$this->getUserID(),
			$this->getMitgliedID(),
			$this->getMitgliedschaftID(),
			$this->getGliederungID(),
			$this->isGeloescht(),
			$this->isMitgliedPiraten(),
			$this->isVerteilerEingetragen(),
			$this->getBeitrag(),
			$this->getNatPersonID(),
			$this->getJurPersonID(),
			$this->getKontaktID() ));
	}
}

?>
