<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class File extends StorageClass {
	private $fileid;
	private $filename;
	private $exportfilename;
	private $mimetype;

	public static function factory(Storage $storage, $row) {
		$tempfile = new File($storage);
		$tempfile->setFileID($row["fileid"]);
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

	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$this->setFileID($storage->setFile(
			$this->getFileID(),
			$this->getFileName(),
			$this->getExportFilename(),
			$this->getMimeType()));
	}
}

?>
