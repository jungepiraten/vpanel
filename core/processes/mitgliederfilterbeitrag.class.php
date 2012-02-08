<?php

require_once(VPANEL_CORE . "/process.class.php");

class MitgliederFilterBeitragProcess extends Process {
	private $beitragid;

	private $beitrag;
	private $filter;

	public static function factory(Storage $storage, $row) {
		$process = new MitgliederFilterBeitragProcess($storage);
		$process->setFilter($row["filter"]);
		$process->setBeitragID($row["beitragid"]);
		return $process;
	}

	public function getFilterID() {
		return $this->getFilter()->getFilterID();
	}

	public function getFilter() {
		return $this->filter;
	}

	public function setFilter($filter) {
		$this->filter = $filter;
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
		return array("filter" => $this->getFilter(), "beitragid" => $this->getBeitragID());
	}

	public function runProcess() {
		$result = $this->getStorage()->getMitgliederResult($this->getFilter());
		$max = $result->getCount();
		$i = 0;
		$stepwidth = max(1, ceil($max / 100));

		while ($mitglied = $result->fetchRow()) {
			if ($mitglied->getBeitrag($this->getBeitragID()) == null) {
				$mitglied->setBeitrag($this->getBeitrag(), $this->getBeitragHoehe($mitglied), 0);
			}
			
			if ((++$i % $stepwidth) == 0) {
				$this->setProgress($i / $max);
				$this->save();
			}
		}
		
		$this->setProgress(1);
		$this->save();
	}
}

?>
