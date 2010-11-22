<?php

require_once(VPANEL_CORE . "/globalobject.class.php");

class Mitglied extends GlobalClass {
	private $mitgliedid;
	private $eintrittsdatum;
	private $austritttsdatum;
	
	private $revisions = array();
	
	public static function factoryByMitgliedID(Storage $storage, $mitgliedid) {
		$mitglied = new Mitglied($storage);
		$mitglied->setMitgliedID($mitgliedid);
		$mitglied->load();
		return $mitglied;
	}

	public function getMitgliedID() {
		return $this->mitgliedid;
	}

	public function setMitgliedID($mitgliedid) {
		$this->mitgliedid = $mitgliedid;
	}

	public function getEintrittsdatum() {
		return $this->eintrittsdatum;
	}

	public function setEintrittsdatum($eintrittsdatum) {
		$this->eintrittsdatum = $eintrittsdatum;
	}

	public function getAustrittsdatum() {
		return $this->austrittsdatum;
	}

	public function setAustrittsdatum($austrittsdatum) {
		$this->austrittsdatum = $austrittsdatum;
	}

	public function istMitglied() {
		return $this->austrittsdatum == null;
	}
	
	public function getRevision($revisionid) {
		// TODO getFromStorage
		if (!isset($this->revisions[$revisionid])) {
			$this->revisions[$revisionid] = $this->getStorage()->getMitgliedRevision($revisionid);
		}
		return $this->revisions[$revisionid];
	}

	public function getLatestRevision() {
		// TODO ...
		return $this->getRevision(first($this->getRevisionIDs()));
	}
	
	public function getRevisionIDs() {
		return array_keys($this->revisions);
	}

	public function load() {
		if ($this->mitgliederid != null) {
			$row = $storage->getMitglied($this->mitgliederid);
			$this->setMitgliedID($row["mitgliedid"]);
			$this->setGlobalID($row["globalid"]);
			$this->setEintrittsdatum($row["eintrittsdatum"]);
			$this->setAustrittsdatum($row["austrittsdatum"]);
		}
	}

	public function save(Storage $storage = null) {
		if ($storage === null) {
			$storage = $this->getStorage();
		}
		$storage->setMitglied(
			$this->getMitgliedID(),
			$this->getGlobalID(),
			$this->getEintrittsdatum(),
			$this->getAustrittsdatum() );
		// TODO revisions speichern
	}
}

?>
