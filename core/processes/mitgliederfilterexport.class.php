<?php

require_once(VPANEL_PROCESSES . "/mitgliederfilter.class.php");

class MitgliederFilterExportProcess extends MitgliederFilterProcess {
	private $fields = array();
	private $streamhandler;

	public static function factory(Storage $storage, $row) {
		$process = parent::factory($storage, $row);
		$process->setMatcher($row["matcher"]);
		$process->setFields($row["fields"]);
		$process->setStreamHandler($row["streamhandler_class"]::factory($storage, $process, $row["streamhandler"]));
		return $process;
	}

	public function getFields() {
		return $this->fields;
	}

	public function setFields($fields) {
		$this->fields = $fields;
	}

	public function getStreamHandler() {
		return $this->streamhandler;
	}

	public function setStreamHandler($streamhandler) {
		$streamhandler->setStorage($this->getStorage());
		$streamhandler->setProcess($this);
		$this->streamhandler = $streamhandler;
	}
	
	protected function getData() {
		$data = parent::getData();
		$data["streamhandler_class"] = get_class($this->getStreamHandler());
		$data["streamhandler"] = $this->getStreamHandler()->getData();
		$data["fields"] = $this->getFields();
		return $data;
	}

	protected function initProcess() {
		$this->getStreamHandler()->openFile(array_keys($this->getFields()));
	}

	protected function runProcessStep($mitglied) {
		$row = array();
		foreach ($this->getFields() as $field => $template) {
			$row[$field] = $mitglied->replaceText($template);
		}
		$this->getStreamHandler()->writeFile($row);
	}

	protected function finalizeProcess() {
		$this->getStreamHandler()->closeFile();
	}

	public function delete() {
		$this->getStreamHandler->delete();
		parent::delete();
	}
}

abstract class ExportStreamHandler {
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

abstract class TempFileExportStreamHandler extends ExportStreamHandler {
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
		if ($this->file != null) {
			$this->file->delete();
		}
	}
}

class CSVTempFileExportStreamHandler extends TempFileExportStreamHandler {
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

	public function delete() {
		$this->getFile()->delete();
	}
}

?>
