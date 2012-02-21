<?php

require_once(VPANEL_CORE . "/process.class.php");

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

	public function runPrepareData($progressOffset, $progress) {
		$result = $this->getStorage()->getMitgliederResult($this->getMatcher());
		
		$deltaScale = array();
		$this->maxMitgliederTime = $this->minMitgliederTime = floor(time() / $this->scalaMitgliederTime);
		while ($mitglied = $result->fetchRow()) {
			$eintritt = floor($mitglied->getEintrittsdatum() / $this->scalaMitgliederTime);
			$this->minMitgliederTime = min($this->minMitgliederTime, $eintritt);
			$this->maxMitgliederTime = max($this->maxMitgliederTime, $eintritt);
			if (!isset($deltaScale[$eintritt])) {
				$deltaScale[$eintritt] = 0;
			}
			$deltaScale[$eintritt] ++;

			$austritt = floor($mitglied->getAustrittsdatum() / $this->scalaMitgliederTime);
			if ($austritt != null) {
				$this->minMitgliederTime = min($this->minMitgliederTime, $austritt);
				$this->maxMitgliederTime = max($this->maxMitgliederTime, $austritt);
				if (!isset($deltaScale[$austritt])) {
					$deltaScale[$austritt] = 0;
				}
				$deltaScale[$austritt] --;
			}
		}

		$this->setProgress($progressOffset + 0.7 * $progress);
		$this->save();

		$curValue = 0;
		for ($time = $this->minMitgliederTime; $time <= $this->maxMitgliederTime; $time ++) {
			$this->mitgliederCount[$time] = $curValue = $curValue + (isset($deltaScale[$time]) ? $deltaScale[$time] : 0);
			$this->maxMitgliederCount = max($this->maxMitgliederCount, $curValue);
		}

		$this->setProgress($progressOffset + 1 * $progress);
		$this->save();
	}

	public function runGenerateAgeGraph($w, $h, $file, $progressOffset, $progress) {
		
	}

	public function runGenerateTimeGraph($w, $h, $file, $progressOffset, $progress) {
		$offsetX = 40;
		$scalaX = 110; $scalaY = 60;

		$img = ImageCreateTrueColor($offsetX + $w, 20 + $h);
		$white    = ImageColorAllocate($img, 255, 255, 255);
		$color    = ImageColorAllocate($img, 255,   0,   0);
		$boxcolor = ImageColorAllocate($img,  20,  20,  20);
		ImageFilledRectangle($img, 0,0, $offsetX+$w,20+$h, $white);

		$pixelsPerValue = ($h - $scalaY / 3) / $this->maxMitgliederCount;
		$timePerPixel = ($this->maxMitgliederTime - $this->minMitgliederTime) / $w;

		while (round($scalaY / $pixelsPerValue) % 10 != 0) {
			$scalaY ++;
		}
		$scalaY = $pixelsPerValue * round($scalaY / $pixelsPerValue);

		for ($y = 0; $y < $h - imagefontheight(3); $y += $scalaY) {
			ImageString($img, 3, $offsetX - 10, $h - $y - imagefontheight(3), round($y / $pixelsPerValue), $boxcolor);
			ImageLine($img, $offsetX, $h - $y, $offsetX + $w, $h - $y, $boxcolor);
		}

		for ($x = 0; $x <= $w; $x++) {
			$curTime = $this->minMitgliederTime + floor($x * $timePerPixel);
			$curValue = $this->mitgliederCount[$curTime];

			ImageLine($img, $offsetX + $x, $h, $offsetX + $x, $h - round($curValue * $pixelsPerValue), $color);
			if ($x % $scalaX == 0 && $x > 0 && $x < $w - imagefontwidth(3) * 5) {
				ImageString($img, 3, $offsetX + $x - imagefontwidth(3) * 5, $h + 5, date("d.m.Y", $curTime * $this->scalaMitgliederTime), $boxcolor);
			}
		}

		$file->setMimeType("image/png");
		$file->setExportFilename($file->getExportFilename() . ".png");
		$file->save();
		ImagePNG($img, $file->getAbsoluteFileName());

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
