<?php

require_once(VPANEL_CORE . "/storageobject.class.php");
require_once(VPANEL_CORE . "/processes/mitgliederfiltersendmail.class.php");

abstract class Process extends StorageClass {
	private $processid;
	private $progress;
	private $queued;
	private $started;
	private $finished;

	public static function factory(Storage $storage, $row) {
		$process = $row["type"]::factory($storage, unserialize($row["typedata"]));
		$process->setProcessID($row["processid"]);
		$process->setProgress($row["progress"]);
		$process->setQueued($row["queued"]);
		$process->setStarted($row["started"]);
		$process->setFinished($row["finished"]);
		return $process;
	}

	public function __construct(Storage $storage) {
		parent::__construct($storage);
		$this->setQueued(time());
	}

	public function getProcessID() {
		return $this->processid;
	}

	public function setProcessID($processid) {
		$this->processid = $processid;
	}

	public function getProgress() {
		return $this->progress;
	}

	public function setProgress($progess) {
		$this->progress = $progess;
	}

	public function getQueued() {
		return $this->queued;
	}

	public function setQueued($queued) {
		$this->queued = $queued;
	}

	public function getStarted() {
		return $this->started;
	}

	public function setStarted($started) {
		$this->started = $started;
	}

	public function getFinished() {
		return $this->finished;
	}

	public function setFinished($finished) {
		$this->finished = $finished;
	}

	public function isWaiting() {
		return $this->getQueued() != null && $this->getStarted() == null;
	}

	public function isRunning() {
		return $this->getStarted() != null && $this->getFinished() == null;
	}

	public function isFinished() {
		return $this->getFinished() != null;
	}
	
	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$this->setProcessID( $storage->setProcess(
			$this->getProcessID(),
			get_class($this),
			serialize($this->getData()),
			$this->getProgress(),
			$this->getQueued(),
			$this->getStarted(),
			$this->getFinished() ));
	}

	public function run() {
		$this->setStarted(time());
		$this->save();
		$this->runProcess();
		$this->setFinished(time());
		$this->save();
	}

	abstract public function runProcess();
	abstract protected function getData();
}

?>
