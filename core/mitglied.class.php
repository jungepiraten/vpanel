<?php

require_once(VPANEL_CORE . "/globalobject.class.php");
require_once(VPANEL_CORE . "/mitgliedrevision.class.php");

class Mitglied extends GlobalClass {
	private $mitgliedid;
	private $eintrittsdatum;
	private $austrittsdatum;
	
	private $revisions = array();
	
	public static function factory(Storage $storage, $row) {
		$mitglied = new Mitglied($storage);
		$mitglied->setMitgliedID($row["mitgliedid"]);
		$mitglied->setGlobalID($row["globalid"]);
		$mitglied->setEintrittsdatum($row["eintritt"]);
		$mitglied->setAustrittsdatum($row["austritt"]);
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

	public function isMitglied() {
		return $this->austrittsdatum == null;
	}
	
	public function addRevision($revision) {
		$this->revisions[$revision->getRevisionID()] = $revision;
	}
	
	public function &getRevision($revisionid) {
		if (!isset($this->revisions[$revisionid]) or $this->revisions[$revisionid] == null) {
			$this->revisions[$revisionid] = $this->getStorage()->getMitgliedRevision($revisionid);
		}
		return $this->revisions[$revisionid];
	}

	public function getLatestRevision() {
		// TODO ...
		return $this->getRevision(reset($this->getRevisionIDs()));
	}
	
	public function getRevisionIDs() {
		return array_keys($this->revisions);
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
