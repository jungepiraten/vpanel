<?php

require_once(VPANEL_PROCESSES . "/mitgliederfilter.class.php");

class MitgliederFilterDeleteProcess extends MitgliederFilterProcess {
	private $timestamp;

	public static function factory(Storage $storage, $row) {
		$process = parent::factory($storage, $row);
		$process->setTimestamp($row["timestamp"]);
		return $process;
	}

	public function getTimestamp() {
		return $this->timestamp;
	}

	public function setTimestamp($timestamp) {
		$this->timestamp = $timestamp;
	}

	protected function getData() {
		$data = parent::getData();
		$data["timestamp"] = $this->getTimestamp();
		return $data;
	}

	protected function runProcessStep($mitglied) {
		$mitglied->setAustrittsdatum($this->getTimestamp());
		$mitglied->save();

		$revision = $mitglied->getLatestRevision()->fork();
		$revision->setTimestamp(time());
		$revision->setUserID($this->getUserID());
		$revision->isGeloescht(true);
		$revision->save();
	}
}

?>
