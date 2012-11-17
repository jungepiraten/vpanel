<?php

require_once(VPANEL_PROCESSES . "/mitgliederfilter.class.php");

require_once(VPANEL_CORE . "/graph.class.php");
require_once(VPANEL_CORE . "/chart.class.php");

class MitgliederFilterStatistikProcess extends MitgliederFilterProcess {
	private $timestamp;
	private $mitgliederagemin = 0;
	private $mitgliederagemax = 100;
	private $tempfileids = array();

	public static function factory(Storage $storage, $row) {
		$process = parent::factory($storage, $row);
		$process->setTimestamp($row["timestamp"]);
		$process->setMitgliederAgeRange($row["mitgliederagemin"], $row["mitgliederagemax"]);
		$process->setTempFileIDs($row["tempfileids"]);
		return $process;
	}

	public function getTimestamp() {
		return $this->timestamp;
	}

	public function setTimestamp($timestamp) {
		$this->timestamp = $timestamp;
	}

	public function getMitgliederAgeMin() {
		return $this->mitgliederagemin;
	}

	public function getMitgliederAgeMax() {
		return $this->mitgliederagemax;
	}

	public function setMitgliederAgeRange($min, $max) {
		$this->mitgliederagemin = $min;
		$this->mitgliederagemax = $max;
	}

	public function getMitgliederCountScale() {
		return 24*60*60;
	}

	public function getMitgliederCountStart() {
		return $this->getMitgliederCountEnd() - 5*365*24*60*60;
	}

	public function getMitgliederCountEnd() {
		return floor($this->getTimestamp() / $this->getMitgliederCountScale()) * $this->getMitgliederCountScale();
	}

	public function getTempFileIDs() {
		return $this->tempfileids;
	}

	public function setTempFileIDs($tempfileids) {
		$this->tempfileids = $tempfileids;
	}

	private function generateTempFile() {
		$tempfile = new TempFile($this->getStorage());
		$file = new File($this->getStorage());
		$file->setExportFilename("vpanel-chart-" . date("Y-m-d"));
		$file->save();
		$tempfile->setFile($file);
		$tempfile->setTimestamp(time());
		$tempfile->setUserID($this->getUserID());
		$tempfile->save();
		$this->tempfileids[] = $tempfile->getTempFileID();
		return $tempfile;
	}

	protected function getData() {
		$data = parent::getData();
		$data["timestamp"] = $this->getTimestamp();
		$data["mitgliederagemin"] = $this->getMitgliederAgeMin();
		$data["mitgliederagemax"] = $this->getMitgliederAgeMax();
		$data["tempfileids"] = $this->getTempFileIDs();
		return $data;
	}

	private $mitgliederCount = array();
	private $curMitgliederCount = 0;
	private $maxMitgliederCount = 1;

	private $mitgliederEintritte = array();
	private $maxMitgliederEintritte = 0;
	private $mitgliederAustritte = array();
	private $maxMitgliederAustritte = 0;

	private $factors = array();
	private $mitgliederFactorCount = array();

	public function initProcess() {
		$this->factors[0] = "{GLIEDERUNG}";
		$this->mitgliederFactorCount[0] = array();

		$this->factors[1] = "{STATE}";
		$this->mitgliederFactorCount[1] = array();

		$this->factors[2] = "{MITGLIEDSCHAFT}";
		$this->mitgliederFactorCount[2] = array();

		$this->factors[3] = "{ALTER." . date("Ymd", $this->getTimestamp()) . "}";
		$this->mitgliederFactorCount[3] = array_fill($this->getMitgliederAgeMin(), $this->getMitgliederAgeMax() - $this->getMitgliederAgeMin(), 0);
	}

	public function runProcessStep($mitglied) {
		if ($mitglied->getEintrittsdatum() <= $this->getTimestamp()) {
			$eintritt = $this->normMitgliederCountTime($mitglied->getEintrittsdatum());
			if ($eintritt < $this->getMitgliederCountStart()) {
				$this->curMitgliederCount ++;
			} else {
				$this->santizeArray($this->mitgliederEintritte, $eintritt);
				$this->mitgliederEintritte[$eintritt] ++;
				$this->maxMitgliederEintritte = max($this->maxMitgliederEintritte, $this->mitgliederEintritte[$eintritt]);
			}
			if ($mitglied->getAustrittsdatum() != null) {
				$austritt = $this->normMitgliederCountTime($mitglied->getAustrittsdatum());
				if ($austritt < $this->getMitgliederCountStart()) {
					$this->curMitgliederCount --;
				} else {
					$this->santizeArray($this->mitgliederAustritte, $austritt);
					$this->mitgliederAustritte[$austritt] ++;
					$this->maxMitgliederAustritte = max($this->maxMitgliederAustritte, $this->mitgliederAustritte[$austritt]);
				}
			}
			// Bis zum Ende des Graphen noch nicht Ausgetreten
			if ($mitglied->getAustrittsdatum() == null || $mitglied->getAustrittsdatum() > $this->getMitgliederCountEnd()) {
				$revision = $mitglied->getLatestRevision();
				foreach ($this->factors as $i => $factorString) {
					$mitgliedString = $mitglied->replaceText($factorString);
					$this->santizeArray($this->mitgliederFactorCount[$i], $mitgliedString);
					$this->mitgliederFactorCount[$i][$mitgliedString] ++;
				}
			}
		}
	}

	public function finalizeProcess() {
		for ($time = $this->getMitgliederCountStart(); $time <= $this->getMitgliederCountEnd(); $time += $this->getMitgliederCountScale()) {
			$this->santizeArray($this->mitgliederEintritte, $time);
			$this->santizeArray($this->mitgliederAustritte, $time);
			$this->mitgliederCount[$time] = $this->curMitgliederCount = $this->curMitgliederCount + $this->mitgliederEintritte[$time] - $this->mitgliederAustritte[$time];
			$this->maxMitgliederCount = max($this->maxMitgliederCount, $this->curMitgliederCount);
		}

		$this->runGenerateTimeGraph(600, 250, $this->generateTempFile()->getFile());
		$this->runGenerateBalanceTimeGraph(600, 250, $this->generateTempFile()->getFile());
		$this->runGenerateFactorPieChart(450,250, 0, $this->generateTempFile()->getFile());
		$this->runGenerateFactorPieChart(450,250, 1, $this->generateTempFile()->getFile());
		$this->runGenerateFactorPieChart(450,250, 2, $this->generateTempFile()->getFile());
		$this->runGenerateFactorVBarChart(600, 250, 3, $this->generateTempFile()->getFile());
	}

	private function normMitgliederCountTime($value) {
		return $this->getMitgliederCountStart() + floor(($value - $this->getMitgliederCountStart()) / $this->getMitgliederCountScale()) * $this->getMitgliederCountScale();
	}

	private function santizeArray(&$array, $key) {
		if (!isset($array[$key])) {
			$array[$key] = 0;
		}
	}

	private function runGenerateTimeGraph($w, $h, $file) {
		$graph = new Graph($w, $h);
		$graph->setXAxis(new Graph_TimestampAxis($this->getMitgliederCountStart(), $this->getMitgliederCountEnd(), "d.m.Y", $this->getMitgliederCountScale()));
		$graph->setYAxis(new Graph_DefaultAxis(0, 1.1 * $this->maxMitgliederCount));
		$graph->addData(new Graph_AvgData($this->mitgliederCount));
		$graph->plot($file);
	}

	private function runGenerateBalanceTimeGraph($w, $h, $file) {
		$graph = new Graph($w, $h);
		$graph->setXAxis(new Graph_TimestampAxis($this->getMitgliederCountStart(), $this->getMitgliederCountEnd(), "d.m.Y", $this->getMitgliederCountScale()));
		$graph->setYAxis(new Graph_DefaultAxis(-1.05 * $this->maxMitgliederAustritte, 1.05 * $this->maxMitgliederEintritte));
		$graph->addData(new Graph_SumData($this->mitgliederEintritte, 0,  1, new Graph_Color( 30,240, 30)));
		$graph->addData(new Graph_SumData($this->mitgliederAustritte, 0, -1, new Graph_Color(255,  0,  0)));
		$graph->plot($file);
	}

	private function runGenerateFactorPieChart($w, $h, $factor, $file) {
		$chart = new PieChart($w, $h);
		foreach ($this->mitgliederFactorCount[$factor] as $label => $count) {
			if (!empty($label)) {
				$chart->addData(new Chart_Data($label, $count));
			}
		}
		$chart->plot($file);
	}

	private function runGenerateFactorVBarChart($w, $h, $factor, $file) {
		$chart = new VBarChart($w, $h);
		foreach ($this->mitgliederFactorCount[$factor] as $label => $count) {
			if (!empty($label)) {
				$chart->addData(new Chart_Data($label, $count));
			}
		}
		$chart->plot($file);
	}
}

?>
