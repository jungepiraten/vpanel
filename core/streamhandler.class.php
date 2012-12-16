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

?>
