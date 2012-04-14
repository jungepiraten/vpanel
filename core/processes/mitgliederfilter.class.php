<?php

require_once(VPANEL_CORE . "/process.class.php");

class MitgliederFilterProcess extends Process {
	private $matcher;

	public static function factory(Storage $storage, $row) {
		$process = new $row["class"]($storage);
		$process->setMatcher($row["matcher"]);
		return $process;
	}

	public function getMatcher() {
		return $this->matcher;
	}

	public function setMatcher($matcher) {
		$this->matcher = $matcher;
	}

	protected function getData() {
		$data = parent::getData();
		$data["class"] = get_class($this);
		$data["matcher"] = $this->getMatcher();
		return $data;
	}

	protected function runProcess() {
		global $config;

		$result = $this->getStorage()->getMitgliederResult($this->getMatcher());
		$max = $result->getCount();
		$i = 0;
		$stepwidth = ceil($max / 100);

		$this->initProcess();

		while ($mitglied = $result->fetchRow()) {
			$this->runProcessStep($mitglied);
			
			if ((++$i % $stepwidth) == 0) {
				$this->setProgress($i / $max);
				$this->save();
			}
		}

		$this->finalizeProcess();
		
		$this->setProgress(1);
		$this->save();
	}

	protected function initProcess() {}
	protected function runProcessStep($mitglied) {}
	protected function finalizeProcess() {}
}

?>
