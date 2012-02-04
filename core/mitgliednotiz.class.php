<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class MitgliedNotiz extends StorageClass {
	private $mitgliednotizid;
	private $mitgliedid;
	private $authorid;
	private $timestamp;
	private $kommentar;

	private $mitglied;
	private $author;

	public static function factory(Storage $storage, $row) {
		$mitgliednotiz = new mitgliedNotiz($storage);
		$mitgliednotiz->setMitgliedNotizID($row["mitgliednotizid"]);
		$mitgliednotiz->setMitgliedID($row["mitgliedid"]);
		$mitgliednotiz->setAuthorID($row["author"]);
		$mitgliednotiz->setTimestamp($row["timestamp"]);
		$mitgliednotiz->setKommentar($row["kommentar"]);
		return $mitgliednotiz;
	}

	public function getMitgliedNotizID() {
		return $this->mitgliednotizid;
	}

	public function setMitgliedNotizID($mitgliednotizid) {
		$this->mitgliednotizid = $mitgliednotizid;
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
			$this->mitglied = $this->getStorage()->getmitglied($this->mitgliedid);
		}
		return $this->mitglied;
	}

	public function setMitglied($mitglied) {
		$this->setMitgliedID($mitglied->getMitgliedID());
		$this->mitglied = $mitglied;
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
		$this->setMitgliedNotizID( $storage->setMitgliedNotiz(
			$this->getMitgliedNotizID(),
			$this->getMitgliedID(),
			$this->getAuthorID(),
			$this->getTimestamp(),
			$this->getKommentar() ));
	}
}

?>
