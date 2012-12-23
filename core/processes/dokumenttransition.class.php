<?php

require_once(VPANEL_CORE . "/process.class.php");

class DokumentTransitionProcess extends Process {
	private $matcher;
	private $nextkategorieid;
	private $nextstatusid;
	private $nextidentifier;
	private $nextlabel;
	private $nextdata;
	private $notizkommentar;

	public static function factory(Storage $storage, $row) {
		$process = new $row["class"]($storage);
		$process->setMatcher($row["matcher"]);
		$process->setNextKategorieID($row["nextkategorieid"]);
		$process->setNextStatusID($row["nextstatusid"]);
		$process->setNextIdentifier($row["nextidentifier"]);
		$process->setNextLabel($row["nextlabel"]);
		$process->setNextData($row["nextdata"]);
		$process->setNotizKommentar($row["notizkommentar"]);
		return $process;
	}

	public function getMatcher() {
		return $this->matcher;
	}

	public function setMatcher($matcher) {
		$this->matcher = $matcher;
	}

	public function getNextKategorieID() {
		return $this->nextkategorieid;
	}

	public function setNextKategorieID($kategorieid) {
		$this->nextkategorieid = $kategorieid;
	}

	public function getNextStatusID() {
		return $this->nextstatusid;
	}

	public function setNextStatusID($statusid) {
		$this->nextstatusid = $statusid;
	}

	public function getNextIdentifier() {
		return $this->nextidentifier;
	}

	public function setNextIdentifier($identifier) {
		$this->nextidentifier = $identifier;
	}

	public function getNextLabel() {
		return $this->nextlabel;
	}

	public function setNextLabel($label) {
		$this->nextlabel = $label;
	}

	public function getNextData() {
		return $this->nextdata;
	}

	public function setNextData($data) {
		$this->nextdata = $data;
	}

	public function getNotizKommentar() {
		return $this->notizkommentar;
	}

	public function setNotizKommentar($kommentar) {
		$this->notizkommentar = $kommentar;
	}

	protected function getData() {
		$data = parent::getData();
		$data["class"] = get_class($this);
		$data["matcher"] = $this->matcher;
		$data["nextkategorieid"] = $this->nextkategorieid;
		$data["nextstatusid"] = $this->nextstatusid;
		$data["nextidentifier"] = $this->nextidentifier;
		$data["nextlabel"] = $this->nextlabel;
		$data["nextdata"] = $this->nextdata;
		$data["notizkommentar"] = $this->notizkommentar;
		return $data;
	}

	protected function getResult() {
		return $this->getStorage()->getDokumentResult($this->getMatcher());
	}

	protected function runProcess() {
		$result = $this->getResult();
		$max = $result->getCount();
		$i = 0;
		$stepwidth = ceil($max / 100);

		$this->initProcess();

		while ($item = $result->fetchRow()) {
			$this->processItem($item);

			if ((++$i % $stepwidth) == 0) {
				$this->setProgress($i / $max);
				$this->save();
			}
		}

		$this->finalizeProcess();

		$this->setProgress(1);
		$this->save();
	}

	private function processItem($item) {
		$this->runProcessStep($item);

		$notiz = new DokumentNotiz($this->getStorage());
		$notiz->setDokument($item);
		$notiz->setTimestamp(time());
		$notiz->setAuthorID($this->getUserID());
		$notiz->setKommentar($this->getNotizKommentar());

		if ($this->getNextKategorieID() != null) {
			$item->setDokumentKategorieID($this->getNextKategorieID());
			$notiz->setNextKategorieID($this->getNextKategorieID());
		}

		if ($this->getNextStatusID() != null) {
			$item->setDokumentStatusID($this->getNextStatusID());
			$notiz->setNextStatusID($this->getNextStatusID());
		}

		if ($this->getNextIdentifier() != null) {
			$item->setIdentifier($this->getNextIdentifier());
			$notiz->setNextIdentifier($this->getNextIdentifier());
		}

		if ($this->getNextLabel() != null) {
			$item->setLabel($this->getNextLabel());
			$notiz->setNextLabel($this->getNextLabel());
		}

		if ($this->getNextData() != null) {
			$item->setData($this->getNextData());
		}

		$notiz->save();
		$notiz->notify();
		$item->save();
	}

	public function initProcess() {}
	public function runProcessStep($item) {}
	public function finalizeProcess() {}
}

?>
