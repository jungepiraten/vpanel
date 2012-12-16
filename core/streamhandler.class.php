<?php

abstract class StreamHandler {
	private $storage;
	private $process;

	public static function factory(Storage $storage, $process, $row) {
		$handler = new $row["class"]();
		$handler->setStorage($storage);
		$handler->setProcess($process);
		return $handler;
	}

	protected function getStorage() {
		return $this->storage;
	}

	public function setStorage($storage) {
		$this->storage = $storage;
	}

	protected function getProcess() {
		return $this->process;
	}

	public function setProcess($process) {
		$this->process = $process;
	}

	public function getData() {
		$data = array();
		$data["class"] = get_class($this);
		return $data;
	}

	abstract public function openFile($headers);
	abstract public function writeFile($data);
	abstract public function closeFile();
	abstract public function delete();
}

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

class CSVTempFileStreamHandler extends TempFileStreamHandler {
	private $handler;

	public function openFile($headers) {
		$file = $this->getFile();
		$file->setMimeType("text/csv");
		$file->setExportFilename($file->getExportFilename() . ".csv");
		$file->save();

		$this->handler = fopen($file->getAbsoluteFileName(), "w");
		$this->writeFile($headers);
	}

	public function writeFile($row) {
		fputcsv($this->handler, $row);
	}

	public function closeFile() {
		fclose($this->handler);
	}
}

?>
