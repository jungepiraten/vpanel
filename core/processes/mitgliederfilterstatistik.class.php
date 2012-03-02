<?php

require_once(VPANEL_CORE . "/process.class.php");

require_once(VPANEL_CORE . "/graph.class.php");
require_once(VPANEL_CORE . "/chart.class.php");

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

	private $factors = array();
	private $mitgliederFactorCount = array();

	private function normMitgliederCountTime($value) {
		return $this->getStatistik()->getMitgliederCountStart() + floor(($value - $this->getStatistik()->getMitgliederCountStart()) / $this->getStatistik()->getMitgliederCountScale()) * $this->getStatistik()->getMitgliederCountScale();
	}

	public function runPrepareData($progressOffset, $progress) {
		$result = $this->getStorage()->getMitgliederResult($this->getMatcher());
		$curMitgliederCount = 0;
		while ($mitglied = $result->fetchRow()) {
			if ($mitglied->getEintrittsdatum() <= $this->getStatistik()->getTimestamp()) {
				$eintritt = $this->normMitgliederCountTime($mitglied->getEintrittsdatum());
				if ($eintritt < $this->getStatistik()->getMitgliederCountStart()) {
					$curMitgliederCount ++;
				} else {
					if (!isset($this->mitgliederEintritte[$eintritt])) {
						$this->mitgliederEintritte[$eintritt] = 0;
					}
					$this->mitgliederEintritte[$eintritt] ++;
					$this->maxMitgliederEintritte = max($this->maxMitgliederEintritte, $this->mitgliederEintritte[$eintritt]);
				}

				if ($mitglied->getAustrittsdatum() != null) {
					$austritt = $this->normMitgliederCountTime($mitglied->getAustrittsdatum());
					if ($austritt < $this->getStatistik()->getMitgliederCountStart()) {
						$curMitgliederCount --;
					} else {
						if (!isset($this->mitgliederAustritte[$austritt])) {
							$this->mitgliederAustritte[$austritt] = 0;
						}
						$this->mitgliederAustritte[$austritt] ++;
						$this->maxMitgliederAustritte = max($this->maxMitgliederAustritte, $this->mitgliederAustritte[$austritt]);
					}
				}
				if ($mitglied->getAustrittsdatum() == null || $mitglied->getAustrittsdatum() > $this->getStatistik()->getMitgliederCountEnd()) {
					$revision = $mitglied->getLatestRevision();

					foreach ($this->factors as $i => $factorString) {
						$mitgliedString = $mitglied->replaceText($factorString);
						if (!isset($this->mitgliederFactorCount[$i][$mitgliedString])) {
							$this->mitgliederFactorCount[$i][$mitgliedString] = 0;
						}
						$this->mitgliederFactorCount[$i][$mitgliedString]++;
					}
				}
			}
		}

		$this->setProgress($progressOffset + 0.6 * $progress);
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

		$this->setProgress($progressOffset + 1 * $progress);
		$this->save();
	}

	public function runGenerateTimeGraph($w, $h, $file, $progressOffset, $progress) {
		$graph = new Graph($w, $h);
		$graph->setXAxis(new Graph_TimestampAxis($this->getStatistik()->getMitgliederCountStart(), $this->getStatistik()->getMitgliederCountEnd(), "d.m.Y", $this->getStatistik()->getMitgliederCountScale()));
		$graph->setYAxis(new Graph_DefaultAxis(0, 1.1 * $this->maxMitgliederCount));
		$graph->addData(new Graph_AvgData($this->mitgliederCount));
		$graph->plot($file);

		$this->setProgress($progressOffset + 1 * $progress);
		$this->save();
	}

	public function runGenerateBalanceTimeGraph($w, $h, $file, $progressOffset, $progress) {
		$graph = new Graph($w, $h);
		$graph->setXAxis(new Graph_TimestampAxis($this->getStatistik()->getMitgliederCountStart(), $this->getStatistik()->getMitgliederCountEnd(), "d.m.Y", $this->getStatistik()->getMitgliederCountScale()));
		$graph->setYAxis(new Graph_DefaultAxis(-1.05 * $this->maxMitgliederAustritte, 1.05 * $this->maxMitgliederEintritte));
		$graph->addData(new Graph_SumData($this->mitgliederEintritte, 0,  1, new Graph_Color( 30,240, 30)));
		$graph->addData(new Graph_SumData($this->mitgliederAustritte, 0, -1, new Graph_Color(255,  0,  0)));
		$graph->plot($file);

		$this->setProgress($progressOffset + 1 * $progress);
		$this->save();
	}

	public function runGenerateFactorPieChart($w, $h, $factor, $file, $progressOffset, $progress) {
		$chart = new PieChart($w, $h);
		foreach ($this->mitgliederFactorCount[$factor] as $label => $count) {
			if (!empty($label)) {
				$chart->addData(new Chart_Data($label, $count));
			}
		}
		$chart->plot($file);

		$this->setProgress($progressOffset + 1 * $progress);
		$this->save();
	}

	public function runGenerateFactorVBarChart($w, $h, $factor, $file, $progressOffset, $progress) {
		$chart = new VBarChart($w, $h);
		foreach ($this->mitgliederFactorCount[$factor] as $label => $count) {
			if (!empty($label)) {
				$chart->addData(new Chart_Data($label, $count));
			}
		}
		$chart->plot($file);

		$this->setProgress($progressOffset + 1 * $progress);
		$this->save();
	}

	public function runProcess() {
		$this->factors = array("{GLIEDERUNG}", "{STATE}", "{MITGLIEDSCHAFT}", "{ALTER." . date("Ymd", $this->getStatistik()->getTimestamp()) . "}");
		$this->mitgliederFactorCount[0] = array();
		$this->mitgliederFactorCount[1] = array();
		$this->mitgliederFactorCount[2] = array();
		$this->mitgliederFactorCount[3] = array_fill($this->getStatistik()->getMitgliederAgeMinimum(), $this->getStatistik()->getMitgliederAgeMaximum() - $this->getStatistik()->getMitgliederAgeMinimum(), 0);

		$this->runPrepareData(0, 0.7);

		if ($this->getStatistik()->getAgeGraphFile() != null) {
			$agegraph = $this->getStatistik()->getAgeGraphFile();
		} else {
			$agegraph = new File($this->getStorage());
			$agegraph->setExportFilename("vpanel-agegraph-" . date("Y-m-d"));
			$agegraph->save();
			$this->getStatistik()->setAgeGraphFile($agegraph);
		}
		$this->runGenerateFactorVBarChart(600, 250, 3, $agegraph, 0.7, 0.05);

		if ($this->getStatistik()->getTimeGraphFile() != null) {
			$timegraph = $this->getStatistik()->getTimeGraphFile();
		} else {
			$timegraph = new File($this->getStorage());
			$timegraph->setExportFilename("vpanel-timegraph-" . date("Y-m-d"));
			$timegraph->save();
			$this->getStatistik()->setTimeGraphFile($timegraph);
		}
		$this->runGenerateTimeGraph(600, 250, $timegraph, 0.75, 0.05);

		if ($this->getStatistik()->getTimeBalanceGraphFile() != null) {
			$timebalancegraph = $this->getStatistik()->getTimeBalanceGraphFile();
		} else {
			$timebalancegraph = new File($this->getStorage());
			$timebalancegraph->setExportFilename("vpanel-timebalancegraph-" . date("Y-m-d"));
			$timebalancegraph->save();
			$this->getStatistik()->setTimeBalanceGraphFile($timebalancegraph);
		}
		$this->runGenerateBalanceTimeGraph(600, 250, $timebalancegraph, 0.8, 0.05);

		if ($this->getStatistik()->getGliederungChartFile() != null) {
			$gliederungchart = $this->getStatistik()->getGliederungChartFile();
		} else {
			$gliederungchart = new File($this->getStorage());
			$gliederungchart->setExportFilename("vpanel-gliederungchart-" . date("Y-m-d"));
			$gliederungchart->save();
			$this->getStatistik()->setGliederungChartFile($gliederungchart);
		}
		$this->runGenerateFactorPieChart(450,250, 0, $gliederungchart, 0.85, 0.05);

		if ($this->getStatistik()->getStateChartFile() != null) {
			$statechart = $this->getStatistik()->getStateChartFile();
		} else {
			$statechart = new File($this->getStorage());
			$statechart->setExportFilename("vpanel-statechart-" . date("Y-m-d"));
			$statechart->save();
			$this->getStatistik()->setStateChartFile($statechart);
		}
		$this->runGenerateFactorPieChart(450,250, 1, $statechart, 0.9, 0.05);

		if ($this->getStatistik()->getMitgliedschaftChartFile() != null) {
			$mitgliedschaftchart = $this->getStatistik()->getMitgliedschaftChartFile();
		} else {
			$mitgliedschaftchart = new File($this->getStorage());
			$mitgliedschaftchart->setExportFilename("vpanel-gliederungchart-" . date("Y-m-d"));
			$mitgliedschaftchart->save();
			$this->getStatistik()->setMitgliedschaftChartFile($mitgliedschaftchart);
		}
		$this->runGenerateFactorPieChart(450,250, 2, $mitgliedschaftchart, 0.95, 0.05);

		$this->getStatistik()->save();
	}
}

?>
