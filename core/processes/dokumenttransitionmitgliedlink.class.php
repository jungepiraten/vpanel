<?php

require_once(VPANEL_PROCESSES . "/dokumenttransition.class.php");

class DokumentTransaktionMitgliedLinkProcess extends DokumentTransitionProcess {
	private $mitgliedid;

	public static function factory(Storage $storage, $row) {
		$process = parent::factory($storage, $row);
		$process->setMitgliedID($row["mitgliedid"]);
		return $process;
	}

	public function getMitgliedID() {
		return $this->mitgliedid;
	}

	public function setMitgliedID($mitgliedid) {
		$this->mitgliedid = $mitgliedid;
	}

	public function getData() {
		$data = parent::getData();
		$data["mitgliedid"] = $this->mitgliedid;
		return $data;
	}

	public function initProcess() {
	}

	public function runProcessStep($dokument) {
		$this->getStorage()->addMitgliedDokument($this->getMitgliedID(), $dokument->getDokumentID());
	}

	public function finalizeProcess() {
	}
}

?>
