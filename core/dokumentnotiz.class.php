<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class DokumentNotiz extends StorageClass {
	private $dokumentnotizid;
	private $dokumentid;
	private $authorid;
	private $timestamp;
	private $nextkategorieid;
	private $nextstatusid;
	private $nextlabel;
	private $nextidentifier;
	private $kommentar;

	private $dokument;
	private $author;
	private $dokumentkategorie;
	private $dokumentstatus;

	public static function factory(Storage $storage, $row) {
		$dokumentnotiz = new DokumentNotiz($storage);
		$dokumentnotiz->setDokumentNotizID($row["dokumentnotizid"]);
		$dokumentnotiz->setDokumentID($row["dokumentid"]);
		$dokumentnotiz->setAuthorID($row["author"]);
		$dokumentnotiz->setTimestamp($row["timestamp"]);
		$dokumentnotiz->setNextKategorieID($row["nextKategorie"]);
		$dokumentnotiz->setNextStatusID($row["nextState"]);
		$dokumentnotiz->setNextLabel($row["nextLabel"]);
		$dokumentnotiz->setNextIdentifier($row["nextIdentifier"]);
		$dokumentnotiz->setKommentar($row["kommentar"]);
		return $dokumentnotiz;
	}

	public function getDokumentNotizID() {
		return $this->dokumentnotizid;
	}

	public function setDokumentNotizID($dokumentnotizid) {
		$this->dokumentnotizid = $dokumentnotizid;
	}

	public function getDokumentID() {
		return $this->dokumentid;
	}

	public function setDokumentID($dokumentid) {
		if ($dokumentid != $this->dokumentid) {
			$this->dokument = null;
		}
		$this->dokumentid = $dokumentid;
	}

	public function getDokument() {
		if ($this->dokument == null) {
			$this->dokument = $this->getStorage()->getDokument($this->dokumentid);
		}
		return $this->dokument;
	}

	public function setDokument($dokument) {
		$this->setDokumentID($dokument->getDokumentID());
		$this->dokument = $dokument;
	}

	public function getAuthorID() {
		return $this->authorid;
	}

	public function setAuthorID($authorid) {
		if ($authorid != $this->authorid) {
			$this->author = null;
		}
		$this->authorid = $authorid;
	}

	public function getAuthor() {
		if ($this->author == null) {
			$this->author = $this->getStorage()->getUser($this->authorid);
		}
		return $this->author;
	}

	public function setAuthor($author) {
		$this->setAuthorID($author->getUserID());
		$this->author = $author;
	}

	public function getTimestamp() {
		return $this->timestamp;
	}

	public function setTimestamp($timestamp) {
		$this->timestamp = $timestamp;
	}

	public function getNextKategorieID() {
		return $this->nextkategorieid;
	}

	public function setNextKategorieID($nextkategorieid) {
		if ($nextkategorieid != $this->nextkategorieid) {
			$this->nextkategorie = null;
		}
		$this->nextkategorieid = $nextkategorieid;
	}

	public function getNextKategorie() {
		if ($this->nextkategorie == null) {
			$this->nextkategorie = $this->getStorage()->getDokumentKategorie($this->nextkategorieid);
		}
		return $this->nextkategorie;
	}

	public function setNextKategorie($nextkategorie) {
		$this->setNextKategorieID($nextkategorie->getDokumentKategorieID());
		$this->nextkategorie = $nextkategorie;
	}

	public function getNextStatusID() {
		return $this->nextstatusid;
	}

	public function setNextStatusID($nextstatusid) {
		if ($nextstatusid != $this->nextstatusid) {
			$this->nextstatus = null;
		}
		$this->nextstatusid = $nextstatusid;
	}

	public function getNextStatus() {
		if ($this->nextstatus == null) {
			$this->nextstatus = $this->getStorage()->getDokumentStatus($this->nextstatusid);
		}
		return $this->nextstatus;
	}

	public function setNextStatus($nextstatus) {
		$this->setNextStatusID($nextstatus->getDokumentStatusID());
		$this->nextstatus = $nextstatus;
	}

	public function getNextLabel() {
		return $this->nextlabel;
	}

	public function setNextLabel($nextlabel) {
		$this->nextlabel = $nextlabel;
	}

	public function getNextIdentifier() {
		return $this->nextidentifier;
	}

	public function setNextIdentifier($nextidentifier) {
		$this->nextidentifier = $nextidentifier;
	}

	public function getKommentar() {
		return $this->kommentar;
	}

	public function setKommentar($kommentar) {
		$this->kommentar = $kommentar;
	}

	public function save(Storage $storage = null) {
		if ($storage === null) {
			$storage = $this->getStorage();
		}
		$this->setDokumentNotizID( $storage->setDokumentNotiz(
			$this->getDokumentNotizID(),
			$this->getDokumentID(),
			$this->getAuthorID(),
			$this->getTimestamp(),
			$this->getNextKategorieID(),
			$this->getNextStatusID(),
			$this->getNextLabel(),
			$this->getNextIdentifier(),
			$this->getKommentar() ));
	}

	public function delete(Storage $storage = null) {
		if ($storage === null) {
			$storage = $this->getStorage();
		}
		$storage->delDokumentNotiz($this->getDokumentNotizID());
	}

	public function hasPrevNotiz() {
		return $this->getPrevNotiz() != null;
	}

	public function getPrevNotiz() {
		$notizen = $this->getStorage()->getDokumentNotizList($this->getDokumentID());
		foreach ($notizen as $notiz) {
			if ($notiz->getDokumentNotizID() == $this->getDokumentNotizID()) {
				return $prev;
			}
			$prev = $notiz;
		}
	}

	public function notifyUpdate($newnotiz) {
		foreach ($this->getStorage()->getDokumentNotifyList($this->getDokument()->getGliederungID(), $this->getNextKategorieID(), $this->getNextStatusID()) as $notify) {
			$notify->notify($this->getDokument(), $newnotiz, $this);
		}
	}

	public function notify() {
		if ($this->hasPrevNotiz()) {
			$this->getPrevNotiz()->notifyUpdate($this);
		}

		foreach ($this->getStorage()->getDokumentNotifyList($this->getDokument()->getGliederungID(), $this->getNextKategorieID(), $this->getNextStatusID()) as $notify) {
			$notify->notify($this->getDokument(), $this);
		}
	}
}

?>
