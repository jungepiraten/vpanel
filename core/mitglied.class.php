<?php

require_once(VPANEL_CORE . "/globalobject.class.php");
require_once(VPANEL_CORE . "/mitgliedrevision.class.php");

class Mitglied extends GlobalClass {
	private $mitgliedid;
	private $eintrittsdatum;
	private $austritttsdatum;
	
	private $revisions = array();
	
	public static function factory(Storage $storage, $row) {
		$mitglied = new Mitglied($storage);
		$mitglied->setMitgliedID($row["mitgliedid"]);
		$mitglied->setGlobalID($row["globaleid"]);
		$mitglied->setEintrittsdatum($row["eintrittsdatum"]);
		$mitglied->setAustrittsdatum($row["austritsdatum"]);
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
			$row = $storage->getMitglied($this->getMitgliederID());
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
		$this->setMitgliedID( $storage->setMitglied(
			$this->getMitgliedID(),
			$this->getGlobalID(),
			$this->getEintrittsdatum(),
			$this->getAustrittsdatum() ));
		// TODO revisions speichern
	}
}

?>
