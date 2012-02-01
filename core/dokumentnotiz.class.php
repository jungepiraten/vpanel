<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class DokumentNotiz extends StorageClass {
	private $dokumentnotizid;
	private $dokumentid;
	private $authorid;
	private $timestamp;
	private $nextkategorieid;
	private $nextstatusid;
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
		$dokumentnotiz->setDokumentKategorieID($row["dokumentkategorieid"]);
		$dokumentnotiz->setDokumentStatusID($row["dokumentstatusid"]);
		$dokumentnotiz->setKommentar($row["kommentar"]);
		return $dokumentnotiz;
	}

	public function getDokumentNotizID() {
		return $this->dokumentnotizid;
	}

	public function setDokumentNotizID($dokumentnotizid) {
		$this->dokumentnotizid = $dokumentnotizid;
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
			$this->author = $this->getSession()->getUser($this->authorid);
		}
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
			$this->getKommentar() ));
	}
}

?>
