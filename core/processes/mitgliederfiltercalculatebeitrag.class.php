<?php

require_once(VPANEL_PROCESSES . "/mitgliederfilter.class.php");

class MitgliederFilterCalculateBeitragProcess extends MitgliederFilterProcess {
	private $starttimestamp;
	private $endtimestamp;
	private $beitragid;
	private $gliederungsAnteile;

	private $beitrag;

	private $gliederungsHoehe = array();
	private $wunschHoehe = array();
	private $sumHoehe = 0;

	public static function factory(Storage $storage, $row) {
		$process = parent::factory($storage, $row);
		$process->setStartTimestamp($row["starttimestamp"]);
		$process->setEndTimestamp($row["endtimestamp"]);
		$process->setBeitragID($row["beitragid"]);
		$process->setGliederungsAnteile($row["gliederungsAnteile"]);
		$process->setGliederungsHoehe($row["gliederungsHoehe"]);
		$process->setWunschHoehe($row["wunschHoehe"]);
		$process->setSumHoehe($row["sumHoehe"]);
		return $process;
	}

	public function getStartTimestamp() {
		return $this->starttimestamp;
	}

	public function setStartTimestamp($starttimestamp) {
		$this->starttimestamp = $starttimestamp;
	}

	public function getEndTimestamp() {
		return $this->endtimestamp;
	}

	public function setEndTimestamp($endtimestamp) {
		$this->endtimestamp = $endtimestamp;
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

	public function getGliederungsAnteile() {
		return $this->gliederungsAnteile;
	}

	public function setGliederungsAnteile($gliederungsanteile) {
		$this->gliederungsAnteile = $gliederungsanteile;
	}

	public function getGliederungsHoehe() {
		return $this->gliederungsHoehe;
	}

	public function setGliederungsHoehe($gliederungsHoehe) {
		$this->gliederungsHoehe = $gliederungsHoehe;
	}

	public function getWunschHoehe() {
		return $this->wunschHoehe;
	}

	public function setWunschhoehe($wunschHoehe) {
		$this->wunschHoehe = $wunschHoehe;
	}

	public function getSumHoehe() {
		return $this->sumHoehe;
	}

	public function setSumHoehe($sumHoehe) {
		$this->sumHoehe = $sumHoehe;
	}
	
	protected function getData() {
		$data = parent::getData();
		$data["starttimestamp"] = $this->getStartTimestamp();
		$data["endtimestamp"] = $this->getEndTimestamp();
		$data["beitragid"] = $this->getBeitragID();
		$data["gliederungsAnteile"] = $this->getGliederungsAnteile();
		$data["gliederungsHoehe"] = $this->getGliederungsHoehe();
		$data["wunschHoehe"] = $this->getWunschHoehe();
		$data["sumHoehe"] = $this->getSumHoehe();
		return $data;
	}

	protected function runProcessStep($mitglied) {
		$mitgliedbeitrag = $mitglied->getBeitrag($this->getBeitragID());
		foreach ($mitgliedbeitrag->getBuchungen() as $buchung) {
			if ($this->getStartTimestamp() <= $buchung->getTimestamp() && $buchung->getTimestamp() < $this->getEndTimestamp() + 24*60*60) {
				if (!isset($this->gliederungsHoehe[$buchung->getGliederungID()])) {
					$this->gliederungsHoehe[$buchung->getGliederungID()] = 0;
				}
				$this->gliederungsHoehe[$buchung->getGliederungID()] += $buchung->getHoehe();
			}
		}
	}

	protected function finalizeProcess() {
		$this->sumHoehe = 0;
		foreach ($this->gliederungsHoehe as $hoehe) {
			$this->sumHoehe += $hoehe;
		}

		$this->wunschHoehe = array();
		foreach ($this->gliederungsAnteile as $gliederungid => $anteil) {
			$this->wunschHoehe[$gliederungid] = $anteil * $this->sumHoehe;
		}
	}
}

?>
