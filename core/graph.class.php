<?php

class Graph {
	private $width;
	private $height;
	private $xaxis;
	private $yaxis;
	private $data;

	public function __construct($width, $height) {
		$this->width = $width;
		$this->height = $height;
	}

	private function getWidth() {
		return $this->width;
	}

	private function getHeight() {
		return $this->height;
	}

	private function getPlotXAxisPosition() {
		return $this->getPlotHeight() + 5;
	}

	private function getPlotYAxisPosition() {
		return $this->getPlotXOffset() - 5;
	}

	private function getPlotXAxisSize() {
		return 30;
	}

	private function getPlotYAxisSize() {
		return 20;
	}

	private function getPlotXOffset() {
		return $this->getPlotXAxisSize();
	}

	private function getPlotYOffset() {
		return 0;
	}

	private function getPlotWidth() {
		return $this->getWidth() - $this->getPlotXAxisSize();
	}

	private function getPlotHeight() {
		return $this->getHeight() - $this->getPlotYAxisSize();
	}

	private function getXAxis() {
		return $this->xaxis;
	}

	public function setXAxis($axis) {
		$this->xaxis = $axis;
	}

	private function getYAxis() {
		return $this->yaxis;
	}

	public function setYAxis($axis) {
		$this->yaxis = $axis;
	}

	private function getDataValue($key) {
		if (isset($this->data[round($key)])) {
			return $this->data[round($key)];
		}
		return 0;
	}

	public function setData($data) {
		$this->data = $data;
	}

	public function plot($file = null) {
		$img = ImageCreate($this->getWidth(), $this->getHeight());
		$background	= ImageColorAllocate($img, 255,255,255);
		$plotcolor	= ImageColorAllocate($img, 255,  0,  0);
		$axiscolor	= ImageColorAllocate($img,   0,  0,  0);
		$axisfont	= 3;

		// X-Axis
		$posXaxis = 0;
		$lastXpixel_label = null;
		$lastXpixel_line = null;
		while ($this->getXAxis()->getLabelPosition($posXaxis) < 1) {
			$labelPosition = $this->getXAxis()->getLabelPosition($posXaxis);
			$label = $this->getXAxis()->getLabel($labelPosition);
			$value = $this->getXAxis()->getValue($labelPosition);
			$xPixel = $this->getXAxis()->getPosition($value) * $this->getPlotWidth();
			$x = $this->getPlotXOffset() + $xPixel;
			$preXpixel = ImageFontWidth($axisfont) * strlen($label) / 2;
			$postXpixel = ImageFontWidth($axisfont) * strlen($label) / 2;

			if ($lastXpixel_label == null || $lastXpixel_label + 20 < $xPixel - $preXpixel) {
				ImageString($img, $axisfont, $x - $preXpixel, $this->getPlotXAxisPosition(), $label, $axiscolor);
				$lastXpixel_label = $xPixel + $postXpixel;
			}
			if ($lastXpixel_line == null || $lastXpixel_line + 20 < $xPixel) {
				ImageLine($img, $x, $this->getPlotYOffset(), $x, $this->getPlotYOffset() + $this->getPlotHeight(), $axiscolor);
				$lastXpixel_line = $xPixel;
			}
			$posXaxis++;
		}

		// Y-Axis
		$posYaxis = 0;
		$lastYpixel_label = null;
		$lastYpixel_line = null;
		while ($this->getYAxis()->getLabelPosition($posYaxis) < 1) {
			$labelPosition = $this->getYAxis()->getLabelPosition($posYaxis);
			$label = $this->getYAxis()->getLabel($labelPosition);
			$value = $this->getYAxis()->getValue($labelPosition);
			$yPixel = $this->getYAxis()->getPosition($value) * $this->getPlotHeight();
			$y = $this->getPlotYOffset() + ($this->getPlotHeight() - $yPixel);
			$preXpixel = ImageFontWidth($axisfont) * strlen($label);
			$preYpixel = ImageFontHeight($axisfont) / 2;
			$postYpixel = ImageFontHeight($axisfont) / 2;

			if ($lastYpixel_label == null || $lastYpixel_label + 20 < $yPixel - $preYpixel) {
				ImageString($img, $axisfont, $this->getPlotYAxisPosition() - $preXpixel, $y - $preYpixel, $label, $axiscolor);
				$lastYpixel_label = $yPixel + $postYpixel;
			}
			if ($lastYpixel_line == null || $lastYpixel_line + 20 < $yPixel) {
				ImageLine($img, $this->getPlotXOffset(), $y, $this->getPlotXOffset() + $this->getPlotWidth(), $y, $axiscolor);
				$lastYpixel_line = $yPixel;
			}
			$posYaxis++;
		}

		// Data
		for ($x = 0; $x <= $this->getPlotWidth(); $x++) {
			$data_key = $this->getXAxis()->getValue($x / $this->getPlotWidth());
			$data_value = $this->getDataValue($data_key);
			$y = $this->getPlotHeight() - $this->getPlotHeight() * $this->getYAxis()->getPosition($data_value);

			ImageLine($img, $this->getPlotXOffset() + $x, $y, $this->getPlotXOffset() + $x, $this->getPlotHeight(), $plotcolor);
		}

		// Output
		if ($file != null) {
			$file->setMimeType("image/png");
			if (substr($file->getExportFilename(), 0, -4) != ".png") {
				$file->setExportFilename($file->getExportFilename() . ".png");
			}
			$file->save();
			ImagePNG($img, $file->getAbsoluteFileName());
		} else {
			header("Content-Type: image/png");
			ImagePNG($img);
		}
	}
}

class Graph_DefaultAxis {
	private $min;
	private $max;

	public function __construct($min, $max) {
		$this->min = $min;
		$this->max = $max;
	}

	private function getMinimum() {
		return $this->min;
	}

	private function getMaximum() {
		return $this->max;
	}

	private function getDelta() {
		return $this->getMaximum() - $this->getMinimum();
	}

	public function getLabelPosition($i) {
		return $this->getPosition($this->getMinimum() + $i * ($this->getDelta() / 10));
	}

	public function getPosition($val) {
		if ($val < $this->getMinimum()) {
			return 0;
		}
		if ($val > $this->getMaximum()) {
			return 1;
		}
		return ($val - $this->getMinimum()) / $this->getDelta();
	}

	public function getValue($pos) {
		return $this->getMinimum() + $pos * $this->getDelta();
	}

	public function getLabel($pos) {
		return round($this->getValue($pos));
	}
}

class Graph_TimestampAxis extends Graph_DefaultAxis {
	private $dateFormat;
	private $timeMulti;
	
	public function __construct($min, $max, $dateFormat, $timeMulti) {
		parent::__construct($min, $max);
		$this->dateFormat = $dateFormat;
		$this->timeMulti = $timeMulti;
	}

	public function getLabel($pos) {
		return date($this->dateFormat, $this->getValue($pos) * $this->timeMulti);
	}
}

?>
