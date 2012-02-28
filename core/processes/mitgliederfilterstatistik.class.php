<?php

require_once(VPANEL_CORE . "/process.class.php");

require_once(VPANEL_CORE . "/graph.class.php");

class MitgliederFilterStatistikProcess extends Process {
	private $statistikid;

	private $matcher;
	private $statistik;

	public static function factory(Storage $storage, $row) {
		$process = new MitgliederFilterStatistikProcess($storage);
		$process->setMatcher($row["matcher"]);
		$process->setStatistikID($row["statistikid"]);
		return $process;
	}

	public function getMatcher() {
		return $this->matcher;
	}

	public function setMatcher($matcher) {
		$this->matcher = $matcher;
	}

	public function getStatistikID() {
		return $this->statistikid;
	}

	public function setStatistikID($statistikid) {
		if ($statistikid != $this->statistikid) {
			$this->statistik = null;
		}
		$this->statistikid = $statistikid;
	}

	public function getStatistik() {
		if ($this->statistik == null) {
			$this->statistik = $this->getStorage()->getMitgliederStatistik($this->getStatistikID());
		}
		return $this->statistik;
	}

	public function setStatistik($statistik) {
		$this->setStatistikID($statistik->getStatistikID());
		$this->statistik = $statistik;
	}

	protected function getData() {
		return array("matcher" => $this->getMatcher(), "statistikid" => $this->getStatistikID());
	}

	private $scalaMitgliederTime = 84600;
	private $minMitgliederTime;
	private $maxMitgliederTime;
	private $mitgliederCount = array();
	private $maxMitgliederCount = 1;

	private $mitgliederStateCount = array();
	private $maxMitgliederStateCount = 1;

	private $minMitgliederAge = 99;
	private $maxMitgliederAge = 0;
	private $mitgliederAgeCount = array();
	private $maxMitgliederAgeCount = 1;

	public function runPrepareData($progressOffset, $progress) {
		foreach ($this->getStorage()->getStateList() as $state) {
			$this->mitgliederStateCount[$state->getStateID()] = 0;
		}
		
		$result = $this->getStorage()->getMitgliederResult($this->getMatcher());
		$deltaScale = array();
		$this->maxMitgliederTime = $this->minMitgliederTime = floor($this->getStatistik()->getTimestamp() / $this->scalaMitgliederTime);
		while ($mitglied = $result->fetchRow()) {
			if ($mitglied->getEintrittsdatum() < $this->getStatistik()->getTimestamp()) {
				$eintritt = floor($mitglied->getEintrittsdatum() / $this->scalaMitgliederTime);
				$this->minMitgliederTime = min($this->minMitgliederTime, $eintritt);
				$this->maxMitgliederTime = max($this->maxMitgliederTime, $eintritt);
				if (!isset($deltaScale[$eintritt])) {
					$deltaScale[$eintritt] = 0;
				}
				$deltaScale[$eintritt] ++;

				if ($mitglied->getAustrittsdatum() != null && $mitglied->getAustrittsdatum() < $this->getStatistik()->getTimestamp()) {
					$austritt = floor($mitglied->getAustrittsdatum() / $this->scalaMitgliederTime);
					$this->minMitgliederTime = min($this->minMitgliederTime, $austritt);
					$this->maxMitgliederTime = max($this->maxMitgliederTime, $austritt);
					if (!isset($deltaScale[$austritt])) {
						$deltaScale[$austritt] = 0;
					}
					$deltaScale[$austritt] --;
				} else {
					$revision = $mitglied->getLatestRevision();

					$this->mitgliederStateCount[$revision->getKontakt()->getOrt()->getStateID()]++;

					if ($revision->isNatPerson()) {
						$geburtsdatum = $revision->getNatPerson()->getGeburtsdatum();
						$age = date("Y", $this->getStatistik()->getTimestamp()) - date("Y", $geburtsdatum);
						if (date("md", $this->getStatistik()->getTimestamp()) < date("md", $geburtsdatum)) {
							$age--;
						}
						if (!isset($this->mitgliederAgeCount[$age])) {
							$this->mitgliederAgeCount[$age] = 0;
						}
						$this->minMitgliederAge = min($this->minMitgliederAge, $age);
						$this->maxMitgliederAge = max($this->maxMitgliederAge, $age);
						$this->mitgliederAgeCount[$age]++;
					}
				}
			}
		}

		$this->setProgress($progressOffset + 0.5 * $progress);
		$this->save();

		$curValue = 0;
		for ($time = $this->minMitgliederTime; $time <= $this->maxMitgliederTime; $time ++) {
			$this->mitgliederCount[$time] = $curValue = $curValue + (isset($deltaScale[$time]) ? $deltaScale[$time] : 0);
			$this->maxMitgliederCount = max($this->maxMitgliederCount, $curValue);
		}

		$this->setProgress($progressOffset + 0.8 * $progress);
		$this->save();

		foreach ($this->mitgliederStateCount as $stateid => $count) {
			$this->maxMitgliederStateCount = max($this->maxMitgliederStateCount, $count);
		}

		$this->setProgress($progressOffset + 0.9 * $progress);
		$this->save();

		for ($age = $this->minMitgliederAge; $age <= $this->maxMitgliederAge; $age++) {
			if (!isset($this->mitgliederAgeCount[$age])) {
				$this->mitgliederAgeCount[$age] = 0;
			}
			$this->maxMitgliederAgeCount = max($this->maxMitgliederAgeCount, $this->mitgliederAgeCount[$age]);
		}

		$this->setProgress($progressOffset + 1 * $progress);
		$this->save();
	}

	public function runGenerateAgeGraph($w, $h, $file, $progressOffset, $progress) {
		$graph = new Graph($w, $h);
		$graph->setXAxis(new Graph_DefaultAxis($this->minMitgliederAge, $this->maxMitgliederAge));
		$graph->setYAxis(new Graph_DefaultAxis(0, $this->maxMitgliederAgeCount));
		$graph->setData($this->mitgliederAgeCount);
		$graph->plot($file);
		
		$this->setProgress($progressOffset + 1 * $progress);
		$this->save();
	}

	public function runGenerateTimeGraph($w, $h, $file, $progressOffset, $progress) {
		$graph = new Graph($w, $h);
		$graph->setXAxis(new Graph_TimestampAxis($this->minMitgliederTime, $this->maxMitgliederTime, "d.m.Y", $this->scalaMitgliederTime));
		$graph->setYAxis(new Graph_DefaultAxis(0, $this->maxMitgliederCount));
		$graph->setData($this->mitgliederCount);
		$graph->plot($file);

		$this->setProgress($progressOffset + 1 * $progress);
		$this->save();
	}

	public function runProcess() {
		$this->runPrepareData(0, 0.6);
		$this->runGenerateAgeGraph(600, 250, $this->getStatistik()->getAgeGraphFile(), 0.6, 0.2);
		$this->runGenerateTimeGraph(600, 250, $this->getStatistik()->getTimeGraphFile(), 0.8, 0.2);
	}
}

?>
