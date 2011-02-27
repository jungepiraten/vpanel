<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class TempFile extends StorageClass {
	private $fileid;
	private $userid;
	private $filename;
	private $mimetype;

	private $allowed;
	
	public static function factory(Storage $storage, $row) {
		$tempfile = new TempFile($storage);
		$tempfile->setFileID($row["fileid"]);
		$tempfile->setUserID($row["userid"]);
		$tempfile->setFilename($row["filename"]);
		$tempfile->setExportFilename($row["exportfilename"]);
		$tempfile->setMimeType($row["mimetype"]);
		return $tempfile;
	}

	public function setFileID($fileid) {
		$this->fileid = $fileid;
	}

	public function getFileID() {
		return $this->fileid;
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
			$this->user = $this->getSession()->getUser($this->getUserID());
		}
		return $this->user;
	}

	public function setFilename($filename) {
		$this->filename = $filename;
	}
	
	public function getFilename() {
		if ($this->filename == null) {
			$this->filename = tempnam(sys_get_temp_dir(), "vpanel");
		}
		return $this->filename;
	}

	public function setExportFilename($exportfilename) {
		$this->exportfilename = $exportfilename;
	}
	
	public function getExportFilename() {
		return $this->exportfilename;
	}

	public function setMimeType($mimetype) {
		$this->mimetype = $mimetype;
	}

	public function getMimeType() {
		return $this->mimetype;
	}

	public function isAllowed($user) {
		return $this->userid == $user->getUserID();
	}

	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$this->setFileID($storage->setFile(
			$this->getFileID(),
			$this->getUserID(),
			$this->getFileName(),
			$this->getExportFilename(),
			$this->getMimeType()));
	}
}

?>
