<?php

require_once(VPANEL_PROCESSES . "/mitgliederfilter.class.php");

class MitgliederFilterConsecutiveProcess extends MitgliederFilterProcess {
	private $processes = array();

	public static function factory(Storage $storage, $row) {
		$process = parent::factory($storage, $row);
		$process->setProcesses($row["processes"]);
		return $process;
	}

	public function getProcesses() {
		return $this->processes;
	}

	public function setProcesses($processes) {
		$this->processes = $processes;
	}

	public function addProcess($process) {
		$this->processes[] = $process;
	}

	protected function getData() {
		$data = parent::getData();
		$data["processes"] = $this->getProcesses();
		return $data;
	}

	protected function initProcess() {
		foreach ($this->processes as $process) {
			$process->initProcess();
		}
	}

	protected function runProcessStep($mitglied) {
		foreach ($this->processes as $process) {
			$process->runProcessStep($mitglied);
		}
	}

	protected function finalizeProcess() {
		foreach ($this->processes as $process) {
			$process->finalizeProcess();
		}
	}
}

?>
