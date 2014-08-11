<?php

require_once(VPANEL_CORE . "/storageobject.class.php");
foreach (glob(VPANEL_CORE . "/processes/*.class.php") as $processfile) {
	require_once($processfile);
}

abstract class Process extends StorageClass {
	private $type;
	private $typedata;
	private $processid;
	private $userid;
	private $progress;
	private $queued;
	private $started;
	private $finished;
	private $finishedpage;

	private $user;

	public static function factory(Storage $storage, $row) {
		$process = $row["type"]::factory($storage, unserialize($row["typedata"]));
		$process->setType($row["type"]);
		$process->setTypeData($row["typedata"]);
		$process->setProcessID($row["processid"]);
		$process->setUserID($row["userid"]);
		$process->setProgress($row["progress"]);
		$process->setQueued($row["queued"]);
		$process->setStarted($row["started"]);
		$process->setFinished($row["finished"]);
		$process->setFinishedPage($row["finishedpage"]);
		return $process;
	}

	public function __construct(Storage $storage) {
		parent::__construct($storage);
		$this->setQueued(time());
	}

	public function getType() {
		return $this->type;
	}

	public function setType($type) {
		$this->type = $type;
	}

	public function getTypeData() {
		return $this->typedata;
	}

	public function setTypeData($data) {
		$this->typedata = $data;
	}

	public function getProcessID() {
		return $this->processid;
	}

	public function setProcessID($processid) {
		$this->processid = $processid;
	}

	public function getUserID() {
		return $this->userid;
	}

	public function setUserID($userid) {
		if ($this->userid == $userid) {
			$this->user = null;
		}
		$this->userid = $userid;
	}

	public function getUser() {
		if ($this->user == null) {
			$this->user = $this->getStorage()->getUser($this->getUserID());
		}
		return $this->user;
	}

	public function setUser($user) {
		$this->setUserID($user->getUserID());
		$this->user = $user;
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

	public function getFinishedPage() {
		return $this->finishedpage;
	}

	public function setFinishedPage($finishedpage) {
		$this->finishedpage = $finishedpage;
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
			$this->getUserID(),
			get_class($this),
			serialize($this->getData()),
			$this->getProgress(),
			$this->getQueued(),
			$this->getStarted(),
			$this->getFinished(),
			$this->getFinishedPage() ));
	}

	public function delete(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$storage->delProcess($this->getProcessID());
	}

	public function run() {
		$this->setStarted(time());
		$this->save();
		$this->runProcess();
		$this->setFinished(time());
		$this->save();
	}

	abstract protected function runProcess();
	protected function getData() {
		return array();
	}
}

?>
