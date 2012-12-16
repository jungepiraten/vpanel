<?php

require_once(VPANEL_CORE . "/streamhandler.class.php");

abstract class TempFileStreamHandler extends StreamHandler {
	private $tempfile;

	public static function factory(Storage $storage, $process, $row) {
		$handler = parent::factory($storage, $process, $row);
		if (isset($row["tempfileid"])) {
			$handler->setTempFile($storage->getTempFile($row["tempfileid"]));
		}
		return $handler;
	}

	public function getData() {
		$data = parent::getData();
		$data["tempfileid"] = $this->getTempFile()->getTempFileID();
		return $data;
	}

	public function getTempFile() {
		if ($this->tempfile == null) {
			$this->tempfile = new TempFile($this->getStorage());
			$file = new File($this->getStorage());
			$file->setExportFilename("export-" . time());
			$file->save();
			$this->tempfile->setUserID($this->getProcess()->getUserID());
			$this->tempfile->setFile($file);
			$this->tempfile->setTimestamp(time());
			$this->tempfile->save();
		}
		return $this->tempfile;
	}

	protected function setTempFile($tempfile) {
		$this->tempfile = $tempfile;
	}

	protected function getFile() {
		return $this->getTempFile()->getFile();
	}

	public function delete() {
		if ($this->tempfile != null) {
			$this->tempfile->delete();
		}
	}
}

?>
