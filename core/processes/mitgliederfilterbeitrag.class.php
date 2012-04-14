<?php

require_once(VPANEL_PROCESSES . "/mitgliederfilter.class.php");

class MitgliederFilterBeitragProcess extends MitgliederFilterProcess {
	private $beitragid;

	private $beitrag;

	public static function factory(Storage $storage, $row) {
		$process = parent::factory($storage, $row);
		$process->setBeitragID($row["beitragid"]);
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
	
	protected function getData() {
		$data = parent::getData();
		$data["beitragid"] = $this->getBeitragID();
		return $data;
	}

	public function initProcess() {
	}

	public function finalizeProcess() {
	}

	public function runProcessStep($mitglied) {
		if ($mitglied->getBeitrag($this->getBeitragID()) == null) {
			$mitglied->setBeitrag($this->getBeitrag(), $this->getBeitragHoehe($mitglied), 0);
			$mitglied->save();
		}
	}
}

?>
