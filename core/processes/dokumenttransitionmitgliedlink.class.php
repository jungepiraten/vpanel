<?php

require_once(VPANEL_PROCESSES . "/dokumenttransition.class.php");

class DokumentTransaktionMitgliedLinkProcess extends DokumentTransitionProcess {
	private $mitgliedid;

	private $mitglied;

	public static function factory(Storage $storage, $row) {
		$process = parent::factory($storage, $row);
		$process->setMitgliedID($row["mitgliedid"]);
		return $process;
	}

	public function getMitgliedID() {
		return $this->mitgliedid;
	}

	public function setMitgliedID($mitgliedid) {
		if ($mitgliedid != $this->mitgliedid) {
			$this->mitglied = null;
		}
		$this->mitgliedid = $mitgliedid;
	}

	public function getMitglied() {
		if ($this->mitglied == null) {
			$this->mitglied = $this->getStorage()->getMitglied($this->getMitgliedID());
		}
		return $this->mitglied;
	}

	public function setMitglied($mitglied) {
		$this->setMitgliedID($mitglied->getMitgliedID());
		$this->mitglied = $mitglied;
	}

	public function getData() {
		$data = parent::getData();
		$data["mitgliedid"] = $this->mitgliedid;
		return $data;
	}

	public function getNotizKommentar() {
		return $this->getMitglied()->replaceText(parent::getNotizKommentar());
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
