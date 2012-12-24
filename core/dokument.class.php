<?php

require_once(VPANEL_CORE . "/storageobject.class.php");
require_once(VPANEL_MITGLIEDERMATCHER . "/dokument.class.php");

class Dokument extends StorageClass {
	private $dokumentid;

	private $revisions;
	private $latestRevision;

	public static function factory(Storage $storage, $row) {
		$dokument = new Dokument($storage);
		$dokument->setDokumentID($row["dokumentid"]);
		return $dokument;
	}

	public function getDokumentID() {
		return $this->dokumentid;
	}

	public function setDokumentID($dokumentid) {
		$this->dokumentid = $dokumentid;
	}

	public function getRevisionList() {
		if ($this->revisions === null) {
			$this->revisions = array();
			foreach ($this->getStorage()->getDokumentRevisionList($this->getDokumentID()) as $revision) {
				$this->addRevision($revision);
			}
		}
		return $this->revisions;
	}

	public function &getRevision($revisionid) {
		$this->getRevisionList();
		if (!isset($this->revisions[$revisionid]) or $this->revisions[$revisionid] == null) {
			$this->revisions[$revisionid] = $this->getStorage()->getDokumentRevision($revisionid);
		}
		return $this->revisions[$revisionid];
	}

	public function addRevision($revision) {
		$this->getRevisionList();
		$this->revisions[$revision->getRevisionID()] = $revision;
		if (!isset($this->latestRevision) || $revision->getTimestamp() > $this->latestRevision->getTimestamp()) {
			$this->latestRevision = $revision;
		}
	}

	public function getFirstRevision() {
		return reset($this->getRevisionList());
	}

	public function getLatestRevision() {
		if (!isset($this->latestRevision)) {
			$this->latestRevision = $this->getRevision(end($this->getRevisionIDs()));
		}
		return $this->latestRevision;
	}

	public function setLatestRevision($revision) {
		$this->latestRevision = $revision;
	}

	public function getRevisionIDs() {
		return array_map(create_function('$a', 'return $a->getRevisionID();'), $this->getRevisionList());
	}

	public function save(Storage $storage = null) {
		if ($storage === null) {
			$storage = $this->getStorage();
		}
		$this->setDokumentID( $storage->setDokument(
			$this->getDokumentID() ));
	}

	public function delete(Storage $storage = null) {
		if ($storage === null) {
			$storage = $this->getStorage();
		}
		$revisions = $this->getRevisionList();
		foreach ($revisions as $revision) {
			$revision->delete($storage);
		}
		$mitglieder = $storage->getMitgliederList(new DokumentMitgliederMatcher($this->getDokumentID()));
		foreach ($mitglieder as $mitglied) {
			$storage->delMitgliedDokument($mitglied->getMitgliedID(), $this->getDokumentID());
		}
		$storage->delDokument($this->getDokumentID());
	}
}

?>
