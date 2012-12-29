<?php

require_once(VPANEL_PROCESSES . "/dokumenttransition.class.php");

class DokumentTransaktionDownloadProcess extends DokumentTransitionProcess {
	private $tempfile;
	private $ziphandler;

	public static function factory(Storage $storage, $row) {
		$process = parent::factory($storage, $row);
		if (isset($row["tempfileid"])) {
			$process->setTempFile($storage->getTempFile($row["tempfileid"]));
		}
		return $process;
	}

	public function setTempFile($tempfile) {
		$this->tempfile = $tempfile;
	}

	public function getTempFile() {
		if ($this->tempfile == null) {
			$this->tempfile = new TempFile($this->getStorage());
			$file = new File($this->getStorage());
			$file->setMimeType("application/zip");
			$file->setExportFilename("download.zip");
			$file->save();
			$this->tempfile->setUserID($this->getUserID());
			$this->tempfile->setFile($file);
			$this->tempfile->setTimestamp(time());
			$this->tempfile->save();
		}
		return $this->tempfile;
	}

	public function getData() {
		$data = parent::getData();
		$data["tempfileid"] = $this->getTempFile()->getTempFileID();
		return $data;
	}

	public function initProcess() {
		$this->ziphandler = new ZipArchive;
		$this->ziphandler->open($this->getTempFile()->getFile()->getAbsoluteFilename(), ZipArchive::CREATE);
	}

	public function runProcessStep($dokument) {
		$file = $dokument->getLatestRevision()->getFile();
		$this->ziphandler->addFile($file->getAbsoluteFilename(), $file->getExportFilename());
	}

	public function finalizeProcess() {
		$this->ziphandler->close();
	}

	public function delete() {
		if ($this->tempfile != null) {
			$this->tempfile->delete();
		}
		parent::delete();
	}
}

?>
