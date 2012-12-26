<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class DokumentRevision extends StorageClass {
	private $revisionid;
	private $timestamp;
	private $userid;
	private $dokumentid;
	private $gliederungid;
	private $kategorieid;
	private $statusid;
	private $identifier;
	private $label;
	private $content;
	private $data;
	private $fileid;
	private $kommentar;

	private $user;
	private $dokument;
	private $gliederung;
	private $kategorie;
	private $status;
	private $file;
	private $flags;

	public static function factory(Storage $storage, $row) {
		$revision = new DokumentRevision($storage);
		$revision->setRevisionID($row["revisionid"]);
		$revision->setTimestamp($row["timestamp"]);
		$revision->setUserID($row["userid"]);
		$revision->setDokumentID($row["dokumentid"]);
		$revision->setGliederungID($row["gliederungid"]);
		$revision->setKategorieID($row["kategorieid"]);
		$revision->setStatusID($row["statusid"]);
		$revision->setIdentifier($row["identifier"]);
		$revision->setLabel($row["label"]);
		$revision->setContent($row["content"]);
		$revision->setData(unserialize($row["data"]));
		$revision->setFileID($row["fileid"]);
		$revision->setKommentar($row["kommentar"]);
		return $revision;
	}

	public function fork() {
		$r = new DokumentRevision($this->getStorage());
		$r->setDokumentID($this->getDokumentID());
		$r->setGliederungID($this->getGliederungID());
		$r->setKategorieID($this->getKategorieID());
		$r->setStatusID($this->getStatusID());
		$r->setIdentifier($this->getIdentifier());
		$r->setLabel($this->getLabel());
		$r->setContent($this->getContent());
		$r->setData($this->getData());
		$r->setFileID($this->getFileID());
		foreach ($this->getFlags() as $flag) {
			$r->setFlag($flag);
		}
		return $r;
	}

	public function getRevisionID() {
		return $this->revisionid;
	}

	public function setRevisionID($revisionid) {
		$this->revisionid = $revisionid;
	}

	public function getTimestamp() {
		return $this->timestamp;
	}

	public function setTimestamp($timestamp) {
		$this->timestamp = $timestamp;
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

	public function getKategorieID() {
		return $this->kategorieid;
	}

	public function setKategorieID($kategorieid) {
		if ($kategorieid != $this->kategorieid) {
			$this->kategorie = null;
		}
		$this->kategorieid = $kategorieid;
	}

	public function getKategorie() {
		if ($this->kategorie == null) {
			$this->kategorie = $this->getStorage()->getDokumentKategorie($this->kategorieid);
		}
		return $this->kategorie;
	}

	public function setKategorie($kategorie) {
		$this->setKategorieID($kategorie->getDokumentKategorieID());
		$this->kategorie = $kategorie;
	}

	public function getStatusID() {
		return $this->statusid;
	}

	public function setStatusID($statusid) {
		if ($statusid != $this->statusid) {
			$this->status = null;
		}
		$this->statusid = $statusid;
	}

	public function getStatus() {
		if ($this->status == null) {
			$this->status = $this->getStorage()->getDokumentStatus($this->statusid);
		}
		return $this->status;
	}

	public function setStatus($status) {
		$this->setStatusID($status->getDokumentStatusID());
		$this->status = $status;
	}

	public function getIdentifier() {
		return $this->identifier;
	}

	public function setIdentifier($identifier) {
		$this->identifier = $identifier;
	}

	public function getLabel() {
		return $this->label;
	}

	public function setLabel($label) {
		$this->label = $label;
	}

	public function getContent() {
		return $this->content;
	}

	public function setContent($content) {
		$this->content = $content;
	}

	public function getData() {
		return $this->data;
	}

	public function setData($data) {
		$this->data = $data;
	}

	public function getFileID() {
		return $this->fileid;
	}

	public function setFileID($fileid) {
		if ($fileid != $this->fileid) {
			$this->file = null;
		}
		$this->fileid = $fileid;
	}

	public function getFile() {
		if ($this->file == null) {
			$this->file = $this->getStorage()->getFile($this->fileid);
		}
		return $this->file;
	}

	public function setFile($file) {
		$this->setFileID($file->getFileID());
		$this->file = $file;
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
		$this->setRevisionID( $storage->setDokumentRevision(
			$this->getRevisionID(),
			$this->getTimestamp(),
			$this->getUserID(),
			$this->getDokumentID(),
			$this->getGliederungID(),
			$this->getKategorieID(),
			$this->getStatusID(),
			$this->getIdentifier(),
			$this->getLabel(),
			$this->getContent(),
			serialize($this->getData()),
			$this->getFileID(),
			$this->getKommentar() ));

		if ($this->flags != null) {
			$storage->setDokumentRevisionFlagList($this->getRevisionID(), $this->getFlagIDs());
		}
	}

	public function delete(Storage $storage = null) {
		if ($storage === null) {
			$storage = $this->getStorage();
		}
		$storage->setDokumentRevisionFlagList($this->getRevisionID(), array());
		$storage->delDokumentRevision($this->getRevisionID());
	}

	public function hasPrevRevision() {
		return $this->getPrevRevision() != null;
	}

	public function getPrevRevision() {
		$revisionen = $this->getStorage()->getDokumentRevisionList($this->getDokumentID());
		$prev = null;
		foreach ($revisionen as $revision) {
			if ($revision->getRevisionID() == $this->getRevisionID()) {
				return $prev;
			}
			$prev = $revision;
		}
	}

	public function getFlags() {
		if ($this->flags === null) {
			$flags = $this->getStorage()->getDokumentRevisionFlagList($this->getRevisionID());
			$this->flags = array();
			foreach ($flags as $flag) {
				$this->setFlag($flag);
			}
		}
		return $this->flags;
	}
	public function getFlagIDs() {
		return array_keys($this->getFlags());
	}
	public function hasFlag($flagid) {
		$this->getFlags();
		return isset($this->flags[$flagid]);
	}
	public function getFlag($flagid) {
		$this->getFlags();
		return $this->flags[$flagid];
	}
	public function setFlag($flag) {
		$this->getFlags();
		$this->flags[$flag->getFlagID()] = $flag;
	}
	public function delFlag($flagid) {
		$this->getFlags();
		unset($this->flags[$flagid]);
	}

	public function notifyUpdate($newrevision) {
		foreach ($this->getStorage()->getDokumentNotifyList($this->getGliederungID(), $this->getKategorieID(), $this->getStatusID()) as $notify) {
			$notify->notify($this->getDokument(), $newrevision, $this);
		}
	}

	public function notify() {
		if ($this->hasPrevRevision()) {
			$this->getPrevRevision()->notifyUpdate($this);
		}

		foreach ($this->getStorage()->getDokumentNotifyList($this->getGliederungID(), $this->getKategorieID(), $this->getStatusID()) as $notify) {
			$notify->notify($this->getDokument(), $this);
		}
	}
}

?>
