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
			$this->filename = substr(md5(microtime(true) . "-" . rand(100,999)), 0, 12) . "." . array_pop(explode(".", $this->getExportFilename()));
		}
		return $this->filename;
	}

	public function getAbsoluteFilename() {
		return VPANEL_FILES . "/" . $this->getFilename();
	}

	public function getFileSize() {
		if (file_exists($this->getAbsoluteFilename())) {
			return filesize($this->getAbsoluteFilename());
		}
		return 0;
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

	public function exists() {
		return file_exists($this->getAbsoluteFilename());
	}

	public function delete(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		if ($this->exists()) {
			unlink($this->getAbsoluteFilename());
		}
		$storage->delFile($this->getFileID());
	}

	public function getContent() {
		return file_get_contents($this->getAbsoluteFilename());
	}
}

?>
