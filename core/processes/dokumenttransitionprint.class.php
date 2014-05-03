<?php

require_once(VPANEL_PROCESSES . "/dokumenttransition.class.php");

class DokumentTransaktionPrintProcess extends DokumentTransitionProcess {
	private $lpoptions;

	public static function factory(Storage $storage, $row) {
		$process = parent::factory($storage, $row);
		$process->setOptions($row["options"]);
		return $process;
	}

	public function setOptions($options) {
		$this->lpoptions = $options;
	}

	public function getData() {
		$data = parent::getData();
		$data["options"] = $this->lpoptions;
		return $data;
	}

	public function initProcess() {}

	public function runProcessStep($dokument) {
		$file = $dokument->getLatestRevision()->getFile();
		exec("lp " . $this->lpoptions . " -- " . escapeshellarg($file->getAbsoluteFilename()));
	}

	public function finalizeProcess() {}
}

?>
