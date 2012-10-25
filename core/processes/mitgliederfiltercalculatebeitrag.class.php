<?php

require_once(VPANEL_PROCESSES . "/mitgliederfilter.class.php");

class MitgliederFilterCalculateBeitragProcess extends MitgliederFilterProcess {
	private $starttimestamp;
	private $endtimestamp;
	private $beitragid;
	private $gliederungsAnteile;

	private $beitrag;

	private $gliederungsHoehe = array();
	private $gliederungsMitgliedHoehe = array();
	private $gliederungsBeitragHoehe = array();
	private $wunschHoehe = array();
	private $sumHoehe = 0;

	public static function factory(Storage $storage, $row) {
		$process = parent::factory($storage, $row);
		$process->setStartTimestamp($row["starttimestamp"]);
		$process->setEndTimestamp($row["endtimestamp"]);
		$process->setBeitragID($row["beitragid"]);
		$process->setGliederungsAnteile($row["gliederungsAnteile"]);
		$process->setGliederungsMitgliedHoehe($row["gliederungsMitgliedHoehe"]);
		$process->setGliederungsBeitragHoehe($row["gliederungsBeitragHoehe"]);
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

	public function getGliederungsMitgliedHoehe() {
		return $this->gliederungsMitgliedHoehe;
	}

	public function setGliederungsMitgliedHoehe($hoehe) {
		$this->gliederungsMitgliedHoehe = $hoehe;
	}

	public function getGliederungsBeitragHoehe() {
		return $this->gliederungsBeitragHoehe;
	}

	public function setGliederungsBeitragHoehe($hoehe) {
		$this->gliederungsBeitragHoehe = $hoehe;
	}

	public function getWunschHoehe() {
		return $this->wunschHoehe;
	}

	public function setWunschHoehe($hoehe) {
		$this->wunschHoehe = $hoehe;
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
		$data["gliederungsMitgliedHoehe"] = $this->getGliederungsMitgliedHoehe();
		$data["gliederungsBeitragHoehe"] = $this->getGliederungsBeitragHoehe();
		$data["sumHoehe"] = $this->getSumHoehe();
		return $data;
	}

	protected function runProcessStep($mitglied) {
		$mitgliedgliederungid = $mitglied->getLatestRevision()->getGliederungID();
		if (!isset($this->gliederungsHoehe[$mitgliedgliederungid])) {
			$this->gliederungsHoehe[$mitgliedgliederungid] = 0;
		}

		$mitgliedbeitrag = $mitglied->getBeitrag($this->getBeitragID());
		foreach ($mitgliedbeitrag->getBuchungen() as $buchung) {
			if ( ( $this->getStartTimestamp() == NULL || $this->getStartTimestamp() <= $buchung->getTimestamp() )
			  && ( $this->getEndTimestamp() == NULL   || $buchung->getTimestamp() < $this->getEndTimestamp() + 24*60*60 ) ) {
				if (!isset($this->gliederungsBeitragHoehe[$buchung->getGliederungID()])) {
					$this->gliederungsBeitragHoehe[$buchung->getGliederungID()] = 0;
				}
				$this->gliederungsBeitragHoehe[$buchung->getGliederungID()] += $buchung->getHoehe();

				$this->gliederungsHoehe[$mitgliedgliederungid] += $buchung->getHoehe();
			}
		}
	}

	protected function finalizeProcess() {
		$this->sumHoehe = 0;
		foreach ($this->gliederungsHoehe as $gliederungid => $hoehe) {
			$this->sumHoehe += $hoehe;
		}

		$this->gliederungsMitgliedHoehe = array();
		foreach ($this->gliederungsAnteile as $mitgliedgliederungid => $anteile) {
			foreach ($anteile as $beitraggliederungid => $anteil) {
				if (!isset($this->gliederungsMitgliedHoehe[$beitraggliederungid])) {
					$this->gliederungsMitgliedHoehe[$beitraggliederungid] = 0;
				}

				$this->gliederungsMitgliedHoehe[$beitraggliederungid] += $anteil * $this->gliederungsHoehe[$mitgliedgliederungid];
			}
		}
	}
}

?>
