<?php

require_once(VPANEL_PROCESSES . "/dokumenttransition.class.php");

class DokumentTransaktionRenameProcess extends DokumentTransitionProcess {
	public static function factory(Storage $storage, $row) {
		$process = parent::factory($storage, $row);
		return $process;
	}

	public function getData() {
		$data = parent::getData();
		return $data;
	}

	public function initProcess() {
	}

	public function runProcessStep($dokument) {
	}

	public function finalizeProcess() {
	}
}

?>
