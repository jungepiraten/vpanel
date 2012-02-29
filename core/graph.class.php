<?php

class Graph {
	private $width;
	private $height;
	private $xaxis;
	private $yaxis;
	private $data = array();

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

	private function getDataValue($i, $keys) {
		return $this->data[$i]->getValue($keys);
	}

	private function getDataColor($i) {
		$color = $this->data[$i]->getColor();
		if ($color == null) {
			$color = new Graph_Color(255,0,0);
		}
		return $color;
	}

	public function addData($data, $color = null) {
		$this->data[] = $data;
		$this->dataColor[] = $color;
	}

	public function plot($file = null) {
		$img = ImageCreate($this->getWidth(), $this->getHeight());
		$background	= ImageColorAllocate($img, 255,255,255);
		$axiscolor	= ImageColorAllocate($img,   0,  0,  0);
		$axisfont	= 3;
		$dataColor = array();
		for ($i = 0; $i < count($this->data); $i++) {
			$dataColor[$i] = $this->getDataColor($i)->gdAllocate($img);
		}

		// X-Axis
		$posXaxis = 0;
		$lastXpixel_label = null;
		$lastXpixel_line = null;
		while ($this->getXAxis()->getLabelPosition($posXaxis) < 1 && $posXaxis < 30) {
			$labelPosition = $this->getXAxis()->getLabelPosition($posXaxis);
			$label = $this->getXAxis()->getLabel($labelPosition);
			$xPixel = $labelPosition * $this->getPlotWidth();
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
		while ($this->getYAxis()->getLabelPosition($posYaxis) < 1 && $posYaxis < 30) {
			$labelPosition = $this->getYAxis()->getLabelPosition($posYaxis);
			$label = $this->getYAxis()->getLabel($labelPosition);
			$yPixel = $labelPosition * $this->getPlotHeight();
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
		$y_null = $this->getPlotHeight() - $this->getPlotHeight() * $this->getYAxis()->getPosition(0);
		$lastX = null;
		for ($x = 0; $x <= $this->getPlotWidth(); $x++) {
			for ($i = 0; $i < count($this->data); $i++) {
				$data_keys = $this->getXAxis()->getValueList($lastX / $this->getPlotWidth(), $x / $this->getPlotWidth());
				$data_value = $this->getDataValue($i, $data_keys);
				$y = $this->getPlotHeight() - $this->getPlotHeight() * $this->getYAxis()->getPosition($data_value);

				ImageLine($img, $this->getPlotXOffset() + $x, $y, $this->getPlotXOffset() + $x, $y_null, $dataColor[$i]);
			}
			$lastX = $x;
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

class Graph_Color {
	private $r;
	private $g;
	private $b;

	public function __construct($r, $g, $b) {
		$this->r = $r;
		$this->g = $g;
		$this->b = $b;
	}

	public function gdAllocate($img) {
		return ImageColorAllocate($img, $this->r, $this->g, $this->b);
	}
}

abstract class Graph_Data {
	private $data;
	private $defaultvalue;
	private $valuemodifier;
	private $color;

	public function __construct($data, $defaultvalue = 0, $valuemodifier = 1, $color = null) {
		$this->data = $data;
		$this->defaultvalue = $defaultvalue;
		$this->valuemodifier = $valuemodifier;
		$this->color = $color;
	}

	private function hasRawData($key) {
		return isset($this->data[$key]);
	}

	private function getRawData($key) {
		return $this->data[$key];
	}

	abstract protected function normValues($values);

	public function getValue($values) {
		$rawValues = array();
		foreach ($values as $value) {
			if ($this->hasRawData($value)) {
				$rawValues[$value] = $this->getRawData($value);
			}
		}
		if (count($rawValues) == 0) {
			return $this->defaultvalue;
		}
		return $this->normValues($rawValues) * $this->valuemodifier;
	}

	public function getColor() {
		return $this->color;
	}
}

class Graph_AvgData extends Graph_Data {
	protected function normValues($values) {
		return array_sum($values) / count($values);
	}
}

class Graph_SumData extends Graph_Data {
	protected function normValues($values) {
		return array_sum($values);
	}
}

class Graph_DefaultAxis {
	private $min;
	private $max;
	private $precision;

	public function __construct($min, $max, $precision = 1) {
		$this->min = $min;
		$this->max = $max;
		$this->precision = $precision;
	}

	private function getMinimum() {
		return $this->min;
	}

	private function getMaximum() {
		return $this->max;
	}

	private function getPrecision() {
		return $this->precision;
	}

	private function getDelta() {
		return $this->getMaximum() - $this->getMinimum();
	}

	private function roundValue($val) {
		return round($val / $this->getPrecision(), 0) * $this->getPrecision();
	}

	private function floorValue($val) {
		return floor($val / $this->getPrecision()) * $this->getPrecision();
	}

	private function ceilValue($val) {
		return ceil($val / $this->getPrecision()) * $this->getPrecision();
	}

	public function getLabelPosition($i) {
		return $this->getPosition($this->getMinimum() + $i * $this->roundValue($this->getDelta() / 10));
	}

	public function getPosition($val) {
		if ($val < $this->getMinimum()) {
			return 0;
		}
		if ($val > $this->getMaximum()) {
			return 1;
		}
		return ($this->roundValue($val) - $this->getMinimum()) / $this->getDelta();
	}

	private function getValue($pos) {
		$this->roundValue($this->getMinimum() + $pos * $this->getDelta());
	}

	public function getValueList($min, $max) {
		if ($min == null) {
			return array($this->getValue($max));
		}

		$min = $this-> ceilValue($this->getMinimum() + $min * $this->getDelta());
		$max = $this->floorValue($this->getMinimum() + $max * $this->getDelta());

		return range($min, $max, $this->getPrecision());
	}

	public function getLabel($pos) {
		return $this->getValue($pos);
	}
}

class Graph_TimestampAxis extends Graph_DefaultAxis {
	private $dateFormat;
	
	public function __construct($min, $max, $dateFormat, $timeMulti) {
		parent::__construct($min, $max, $timeMulti);
		$this->dateFormat = $dateFormat;
	}

	public function getLabel($pos) {
		return date($this->dateFormat, $this->getValue($pos));
	}
}

?>
