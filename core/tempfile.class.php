<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class TempFile extends StorageClass {
	private $tempfileid;
	private $userid;
	private $fileid;

	private $user;
	private $file;
	
	public static function factory(Storage $storage, $row) {
		$tempfile = new TempFile($storage);
		$tempfile->setTempFileID($row["tempfileid"]);
		$tempfile->setUserID($row["userid"]);
		$tempfile->setFileID($row["fileid"]);
		return $tempfile;
	}

	public function setTempFileID($tempfileid) {
		$this->tempfileid = $tempfileid;
	}

	public function getTempFileID() {
		return $this->tempfileid;
	}

	public function setFileID($fileid) {
		if ($fileid != $this->fileid) {
			$this->file = null;
		}
		$this->fileid = $fileid;
	}

	public function getFileID() {
		return $this->fileid;
	}

	public function setFile($file) {
		$this->setFileID($file->getFileID());
		$this->file = $file;
	}

	public function getFile() {
		if ($this->file == null) {
			$this->file = $this->getStorage()->getFile($this->getFileID());
		}
		return $this->file;
	}

	public function setUserID($userid) {
		if ($userid != $this->userid) {
			$this->user = null;
		}
		$this->userid = $userid;
	}

	public function getUserID() {
		return $this->userid;
	}

	public function setUser($user) {
		$this->setUserID($user->getUserID());
		$this->user = $user;
	}

	public function getUser() {
		if ($this->user == null) {
			$this->user = $this->getStorage()->getUser($this->getUserID());
		}
		return $this->user;
	}

	public function isAllowed($user) {
		return $this->userid == $user->getUserID();
	}

	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$this->setTempFileID($storage->setTempFile(
			$this->getTempFileID(),
			$this->getUserID(),
			$this->getFileID()));
	}
}

?>
