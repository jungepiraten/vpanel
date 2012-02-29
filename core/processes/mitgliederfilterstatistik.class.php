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

	private $mitgliederCount = array();
	private $maxMitgliederCount = 1;

	private $mitgliederEintritte = array();
	private $maxMitgliederEintritte = 0;
	private $mitgliederAustritte = array();
	private $maxMitgliederAustritte = 0;

	private $mitgliederStateCount = array();
	private $maxMitgliederStateCount = 1;

	private $mitgliederAgeCount = array();
	private $maxMitgliederAgeCount = 1;

	public function runPrepareData($progressOffset, $progress) {
		foreach ($this->getStorage()->getStateList() as $state) {
			$this->mitgliederStateCount[$state->getStateID()] = 0;
		}
		
		$result = $this->getStorage()->getMitgliederResult($this->getMatcher());
		$curMitgliederCount = 0;
		while ($mitglied = $result->fetchRow()) {
			if ($mitglied->getEintrittsdatum() <= $this->getStatistik()->getTimestamp()) {
				$eintritt = floor($mitglied->getEintrittsdatum() / $this->getStatistik()->getMitgliederCountScale()) * $this->getStatistik()->getMitgliederCountScale();
				if ($eintritt < $this->getStatistik()->getMitgliederCountStart()) {
					$curMitgliederCount ++;
				} else {
					if (!isset($this->mitgliederEintritte[$eintritt])) {
						$this->mitgliederEintritte[$eintritt] = 0;
					}
					$this->mitgliederEintritte[$eintritt] ++;
					$this->maxMitgliederEintritte = max($this->maxMitgliederEintritte, $this->mitgliederEintritte[$eintritt]);
				}

				if ($mitglied->getAustrittsdatum() != null && $mitglied->getAustrittsdatum() <= $this->getStatistik()->getTimestamp()) {
					$austritt = floor($mitglied->getAustrittsdatum() / $this->getStatistik()->getMitgliederCountScale()) * $this->getStatistik()->getMitgliederCountScale();
					if ($austritt < $this->getStatistik()->getMitgliederCountStart()) {
						$curMitgliederCount --;
					} else {
						if (!isset($this->mitgliederAustritte[$austritt])) {
							$this->mitgliederAustritte[$austritt] = 0;
						}
						$this->mitgliederAustritte[$austritt] ++;
						$this->maxMitgliederAustritte = max($this->maxMitgliederAustritte, $this->mitgliederAustritte[$austritt]);
					}
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
						$this->mitgliederAgeCount[$age]++;
					}
				}
			}
		}

		$this->setProgress($progressOffset + 0.5 * $progress);
		$this->save();

		for ($time = $this->getStatistik()->getMitgliederCountStart(); $time <= $this->getStatistik()->getMitgliederCountEnd(); $time += $this->getStatistik()->getMitgliederCountScale()) {
			if (!isset($this->mitgliederEintritte[$time])) {
				$this->mitgliederEintritte[$time] = 0;
			}
			if (!isset($this->mitgliederAustritte[$time])) {
				$this->mitgliederAustritte[$time] = 0;
			}
			$this->mitgliederCount[$time] = $curMitgliederCount = $curMitgliederCount + $this->mitgliederEintritte[$time] - $this->mitgliederAustritte[$time];
			$this->maxMitgliederCount = max($this->maxMitgliederCount, $curMitgliederCount);
		}

		$this->setProgress($progressOffset + 0.8 * $progress);
		$this->save();

		foreach ($this->mitgliederStateCount as $stateid => $count) {
			$this->maxMitgliederStateCount = max($this->maxMitgliederStateCount, $count);
		}

		$this->setProgress($progressOffset + 0.9 * $progress);
		$this->save();

		for ($age = $this->getStatistik()->getMitgliederAgeMinimum(); $age <= $this->getStatistik()->getMitgliederAgeMaximum(); $age++) {
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
		$graph->setXAxis(new Graph_DefaultAxis($this->getStatistik()->getMitgliederAgeMinimum(), $this->getStatistik()->getMitgliederAgeMaximum()));
		$graph->setYAxis(new Graph_DefaultAxis(0, $this->maxMitgliederAgeCount));
		$graph->addData(new Graph_AvgData($this->mitgliederAgeCount));
		$graph->plot($file);
		
		$this->setProgress($progressOffset + 1 * $progress);
		$this->save();
	}

	public function runGenerateTimeGraph($w, $h, $file, $progressOffset, $progress) {
		$graph = new Graph($w, $h);
		$graph->setXAxis(new Graph_TimestampAxis($this->getStatistik()->getMitgliederCountStart(), $this->getStatistik()->getMitgliederCountEnd(), "d.m.Y", $this->getStatistik()->getMitgliederCountScale()));
		$graph->setYAxis(new Graph_DefaultAxis(0, $this->maxMitgliederCount));
		$graph->addData(new Graph_AvgData($this->mitgliederCount));
		$graph->plot($file);

		$this->setProgress($progressOffset + 1 * $progress);
		$this->save();
	}

	public function runGenerateBalanceTimeGraph($w, $h, $file, $progressOffset, $progress) {
		$graph = new Graph($w, $h);
		$graph->setXAxis(new Graph_TimestampAxis($this->getStatistik()->getMitgliederCountStart(), $this->getStatistik()->getMitgliederCountEnd(), "d.m.Y", $this->getStatistik()->getMitgliederCountScale()));
		$graph->setYAxis(new Graph_DefaultAxis(-1 * $this->maxMitgliederAustritte, $this->maxMitgliederEintritte));
		$graph->addData(new Graph_SumData($this->mitgliederEintritte, 0,  1, new Graph_Color( 30,240, 30)));
		$graph->addData(new Graph_SumData($this->mitgliederAustritte, 0, -1, new Graph_Color(255,  0,  0)));
		$graph->plot($file);

		$this->setProgress($progressOffset + 1 * $progress);
		$this->save();
	}

	public function runProcess() {
		$this->runPrepareData(0, 0.7);

		$agegraph = new File($this->getStorage());
		$agegraph->setExportFilename("vpanel-agegraph-" . date("Y-m-d"));
		$agegraph->save();
		$this->runGenerateAgeGraph(600, 250, $agegraph, 0.7, 0.1);
		$this->getStatistik()->setAgeGraphFile($agegraph);

		$timegraph = new File($this->getStorage());
		$timegraph->setExportFilename("vpanel-timegraph-" . date("Y-m-d"));
		$timegraph->save();
		$this->runGenerateTimeGraph(600, 250, $timegraph, 0.8, 0.1);
		$this->getStatistik()->setTimeGraphFile($timegraph);

		$timebalancegraph = new File($this->getStorage());
		$timebalancegraph->setExportFilename("vpanel-timebalancegraph-" . date("Y-m-d"));
		$timebalancegraph->save();
		$this->runGenerateBalanceTimeGraph(600, 250, $timebalancegraph, 0.9, 0.1);
		$this->getStatistik()->setTimeBalanceGraphFile($timebalancegraph);

		$this->getStatistik()->save();
	}
}

?>
