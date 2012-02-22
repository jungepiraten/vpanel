<?php

require_once(VPANEL_CORE . "/process.class.php");

class MitgliederFilterDeleteProcess extends Process {
	private $userid;
	private $timestamp;

	private $matcher;

	public static function factory(Storage $storage, $row) {
		$process = new MitgliederFilterDeleteProcess($storage);
		$process->setMatcher($row["matcher"]);
		$process->setUserID($row["userid"]);
		$process->setTimestamp($row["timestamp"]);
		return $process;
	}

	public function getMatcher() {
		return $this->matcher;
	}

	public function setMatcher($matcher) {
		$this->matcher = $matcher;
	}

	public function getUserID() {
		return $this->userid;
	}

	public function setUserID($userid) {
		$this->userid = $userid;
	}

	public function getTimestamp() {
		return $this->timestamp;
	}

	public function setTimestamp($timestamp) {
		$this->timestamp = $timestamp;
	}

	protected function getData() {
		return array("matcher" => $this->getMatcher(), "userid" => $this->getUserID(), "timestamp" => $this->getTimestamp());
	}

	public function runProcess() {
		$result = $this->getStorage()->getMitgliederResult($this->getMatcher());
		$max = $result->getCount();
		$i = 0;
		$stepwidth = max(1, ceil($max / 100));

		while ($mitglied = $result->fetchRow()) {
			$mitglied->setAustrittsdatum($this->getTimestamp());
			$mitglied->save();

			$revision = $mitglied->getLatestRevision()->fork();
			$revision->setTimestamp($this->getTimestamp());
			$revision->getUserID($this->getUserID());
			$revision->isGeloescht(true);
			$revision->save();
			
			if ((++$i % $stepwidth) == 0) {
				$this->setProgress($i / $max);
				$this->save();
			}
		}
		
		$this->setProgress(1);
		$this->save();
	}
}

?>
