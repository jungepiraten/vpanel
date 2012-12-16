<?php

require_once(VPANEL_PROCESSES . "/mitgliederfilter.class.php");

class MitgliederFilterExportDebitsProcess extends MitgliederFilterProcess {
	private $streamhandler;
	private $beitragid;

	private $beitrag;

	public static function factory(Storage $storage, $row) {
		$process = parent::factory($storage, $row);
		$process->setBeitragID($row["beitragid"]);
		$process->setStreamHandler($row["streamhandler_class"]::factory($storage, $process, $row["streamhandler"]));
		return $process;
	}

	public function getBeitragID() {
		return $this->beitragid;
	}

	public function setBeitragID($beitragid) {
		if ($beitragid != $this->beitragid) {
			$this->beitrag = null;
		}
		$this->beitragid = $beitragid;
	}

	public function getBeitrag() {
		if ($this->beitrag == null) {
			$this->beitrag = $this->getStorage()->getBeitrag($this->getBeitragID());
		}
		return $this->beitrag;
	}

	public function setBeitrag($beitrag) {
		$this->setBeitragID($beitrag->getBeitragID());
		$this->beitrag = $beitrag;
	}

	public function getBeitragHoehe($mitglied) {
		if ($this->getBeitrag()->getHoehe() == null) {
			return $mitglied->getLatestRevision()->getBeitrag();
		}
		return $this->getBeitrag()->getHoehe();
	}

	public function getStreamHandler() {
		return $this->streamhandler;
	}

	public function setStreamHandler($streamhandler) {
		$streamhandler->setStorage($this->getStorage());
		$streamhandler->setProcess($this);
		$this->streamhandler = $streamhandler;
	}

	protected function getData() {
		$data = parent::getData();
		$data["streamhandler_class"] = get_class($this->getStreamHandler());
		$data["streamhandler"] = $this->getStreamHandler()->getData();
		$data["beitragid"] = $this->getBeitragID();
		return $data;
	}

	protected function initProcess() {
		$this->getStreamHandler()->openFile(array("mitgliedid", "mitglied", "iban", "beitrag", "betrag"));
	}

	protected function runProcessStep($mitglied) {
		if ($mitglied->getLatestRevision()->getKontakt()->hasIBan() && $mitglied->hasBeitrag($this->getBeitragID())) {
			$beitrag = $mitglied->getBeitrag($this->getBeitragID());
			if ($beitrag->getRemainingHoehe() > 0) {
				$row = array();
				$row["mitgliedid"] = $mitglied->getMitgliedID();
				$row["mitglied"] = $mitglied->replaceText("{BEZEICHNUNG}");
				$row["iban"] = $mitglied->getLatestRevision()->getKontakt()->getIBan();
				$row["beitrag"] = $this->getBeitrag()->getLabel();
				$row["betrag"] = $beitrag->getRemainingHoehe();
				$this->getStreamHandler()->writeFile($row);
			}
		}
	}

	protected function finalizeProcess() {
		$this->getStreamHandler()->closeFile();
	}

	public function delete() {
		$this->getStreamHandler()->delete();
		parent::delete();
	}
}

?>
