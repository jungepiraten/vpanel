<?php

require_once(VPANEL_PROCESSES . "/mitgliederfilter.class.php");
require_once(VPANEL_CORE . "/streamhandler.class.php");

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
		$this->getStreamHandler()->delete();
		parent::delete();
	}
}

?>
