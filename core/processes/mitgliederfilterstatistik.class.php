<?php

require_once(VPANEL_CORE . "/process.class.php");

class MitgliederFilterStatistikProcess extends Process {
	private $fileid;

	private $matcher;
	private $file;

	public static function factory(Storage $storage, $row) {
		$process = new $row["class"]($storage);
		$process->setMatcher($row["matcher"]);
		$process->setFileID($row["fileid"]);
		return $process;
	}

	public function getMatcher() {
		return $this->matcher;
	}

	public function setMatcher($matcher) {
		$this->matcher = $matcher;
	}

	public function getFileID() {
		return $this->fileid;
	}

	public function setFileID($fileid) {
		if ($fileid != $this->fileid) {
			$this->file = null;
		}
		$this->fileid = $fileid;
	}

	public function getFile() {
		if ($this->file == null) {
			$this->file = $this->getStorage()->getFile($this->getFileID());
		}
		return $this->file;
	}

	public function setFile($file) {
		$this->setFileID($file->getFileID());
		$this->file = $file;
	}

	public function getFields() {
		return $this->fields;
	}

	protected function getData() {
		return array("class" => get_class($this), "matcher" => $this->getMatcher(), "fileid" => $this->getFileID());
	}

	public function runProcess() {
		$result = $this->getStorage()->getMitgliederResult($this->getMatcher());

		$w = 600; $h = 250;
		$offsetX = 40;
		$scalaX = 110; $scalaY = 60;

		$img = ImageCreateTrueColor($offsetX + $w, 20 + $h);
		$white    = ImageColorAllocate($img, 255, 255, 255);
		$color    = ImageColorAllocate($img, 255,   0,   0);
		$boxcolor = ImageColorAllocate($img,  20,  20,  20);
		ImageFilledRectangle($img, 0,0, $offsetX+$w,20+$h, $white);

		$deltaScale = array();
		$maxTime = $minTime = floor(time() / 84600);
		while ($mitglied = $result->fetchRow()) {
			$eintritt = floor($mitglied->getEintrittsdatum() / 84600);
			$minTime = min($minTime, $eintritt);
			$maxTime = max($maxTime, $eintritt);
			if (!isset($deltaScale[$eintritt])) {
				$deltaScale[$eintritt] = 0;
			}
			$deltaScale[$eintritt] ++;

			$austritt = floor($mitglied->getAustrittsdatum() / 84600);
			if ($austritt != null) {
				$minTime = min($minTime, $austritt);
				$maxTime = max($maxTime, $austritt);
				if (!isset($deltaScale[$austritt])) {
					$deltaScale[$austritt] = 0;
				}
				$deltaScale[$austritt] --;
			}
		}

		$this->setProgress(0.33);
		$this->save();

		$scale = array();
		$curValue = 0;
		$maxValue = 1;
		for ($time = $minTime; $time <= $maxTime; $time ++) {
			$scale[$time] = $curValue = $curValue + (isset($deltaScale[$time]) ? $deltaScale[$time] : 0);
			$maxValue = max($maxValue, $curValue);
		}

		$this->setProgress(0.66);
		$this->save();

		$pixelsPerValue = ($h - $scalaY / 3) / $maxValue;
		$timePerPixel = ($maxTime - $minTime) / $w;

		for ($y = 0; $y < $h; $y += $scalaY) {
			ImageString($img, 3, $offsetX - 10, $h - $y - imagefontheight(3), round($y / $pixelsPerValue), $boxcolor);
			ImageLine($img, $offsetX, $h - $y, $offsetX + $w, $h - $y, $boxcolor);
		}

		for ($x = 0; $x <= $w; $x++) {
			$curTime = $minTime + floor($x * $timePerPixel);
			$curValue = $scale[$curTime];

			ImageLine($img, $offsetX + $x, $h, $offsetX + $x, $h - round($curValue * $pixelsPerValue), $color);
			if ($x % $scalaX == 0 && $x > 0) {
				ImageString($img, 3, $offsetX + $x - imagefontwidth(3) * 10, $h + 5, date("d.m.Y", $curTime * 84600), $boxcolor);
			}
		}

		$this->getFile()->setMimeType("image/png");
		$this->getFile()->setExportFilename($this->getFile()->getExportFilename() . ".png");
		$this->getFile()->save();
		ImagePNG($img, $this->getFile()->getAbsoluteFileName());

		$this->setProgress(1);
		$this->save();
	}
}

?>
