<?php

require_once(VPANEL_CORE . "/process.class.php");

abstract class MitgliederFilterExportProcess extends Process {
	private $fileid;
	private $fields = array();

	private $matcher;
	private $file;

	public static function factory(Storage $storage, $row) {
		$process = new $row["class"]($storage);
		$process->setMatcher($row["matcher"]);
		$process->setFileID($row["fileid"]);
		$process->setFields($row["fields"]);
		return $process;
	}

	public function getMatcher() {
		return $this->matcher;
	}

	public function setMatcher($matcher) {
		$this->matcher = $matcher;
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
			$this->file = $this->getStorage()->getFile($this->getFileID());
		}
		return $this->file;
	}

	public function setFile($file) {
		$this->setFileID($file->getFileID());
		$this->file = $file;
	}

	public function getFields() {
		return $this->fields;
	}

	public function setFields($fields) {
		$this->fields = $fields;
	}

	public function addField($field, $value) {
		$this->fields[$field] = $value;
	}
	
	protected function getData() {
		return array("class" => get_class($this), "matcher" => $this->getMatcher(), "fileid" => $this->getFileID(), "fields" => $this->getFields());
	}

	public function generateFieldsRow($mitglied) {
		$row = array();
		foreach ($this->getFields() as $field => $template) {
			$row[$field] = $mitglied->replaceText($template);
		}
		return $row;
	}

	public function getTableHeading() {
		return array_keys($this->getFields());
	}

	abstract protected function openFile();
	abstract protected function writeFile($data);
	abstract protected function closeFile();

	public function runProcess() {
		$result = $this->getStorage()->getMitgliederResult($this->getMatcher());
		$max = $result->getCount();
		$i = 0;
		$stepwidth = max(1, ceil($max / 100));

		$this->openFile();
		while ($mitglied = $result->fetchRow()) {
			$data = array();
			$this->writeFile($this->generateFieldsRow($mitglied));
			
			if ((++$i % $stepwidth) == 0) {
				$this->setProgress($i / $max);
				$this->save();
			}
		}
		$this->closeFile();
		
		$this->setProgress(1);
		$this->save();
	}
}

class MitgliederFilterExportCSVProcess extends MitgliederFilterExportProcess {
	private $handler;
	
	protected function openFile() {
		$file = $this->getFile();
		$file->setMimeType("text/csv");
		$file->setExportFilename($file->getExportFilename() . ".csv");
		$file->save();
		$this->handler = fopen($file->getAbsoluteFileName(), "w");
		fputcsv($this->handler, $this->getTableHeading());
	}

	protected function writeFile($row) {
		fputcsv($this->handler, $row);
	}

	protected function closeFile() {
		fclose($this->handler);
	}
}

?>
