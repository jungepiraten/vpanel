<?php

require_once(VPANEL_CORE . "/graph.class.php");

class Chart {
	private $width;
	private $height;
	private $data = array();
	private $maxData = 0;
	private $sumData = 0;

	public function __construct($width, $height) {
		$this->width = $width;
		$this->height = $height;
	}

	protected function getWidth() {
		return $this->width;
	}

	protected function getHeight() {
		return $this->height;
	}

	protected function getDataCount() {
		return count($this->data);
	}

	protected function getMaxData() {
		return $this->maxData;
	}

	protected function getDataSum() {
		return $this->sumData;
	}

	protected function getDataLegend($i) {
		return $this->data[$i]->getLegend();
	}

	protected function getDataValue($i) {
		return $this->data[$i]->getValue();
	}

	protected function getDataColor($i) {
		$color = $this->data[$i]->getColor();
		if ($color == null) {
			$hash = hexdec(substr(md5($this->getDataLegend($i)),0,8));
			$r = $hash>>16 & 0xFF;
			$g = $hash >>8 & 0xFF;
			$b = $hash     & 0xFF;
			$color = new Graph_Color($r, $g, $b);
//			$color = new Graph_Color(rand(0,255), rand(0,255), rand(0,255));
		}
		return $color;
	}

	public function addData($data) {
		$this->data[] = $data;
		$this->sumData += $data->getValue();
		$this->maxData = max($this->maxData, $data->getValue());
	}
}

class PieChart extends Chart {
	private function getPieMargin() {
		return 10;
	}

	private function getPieRadius() {
		return floor(min($this->getWidth(), $this->getHeight()) / 2 - $this->getPieMargin());
	}

	private function getPieDiameter() {
		return $this->getPieRadius() * 2;
	}

	private function getPieXPosition() {
		return $this->getPieMargin() + $this->getPieRadius();
	}

	private function getPieYPosition() {
		return $this->getPieMargin() + $this->getPieRadius();
	}

	private function getLegendSpace() {
		return 2;
	}

	private function getLegendPosition($i) {
		if ($i == 0) {
			if ($this->getWidth() > $this->getPieXPosition() + $this->getPieRadius() + $this->getPieMargin()) {
				$x = $this->getPieXPosition() + $this->getPieRadius() + $this->getPieMargin();
				$y = 0;
			} else {
				$x = 0;
				$y = $this->getPieXPosition() + $this->getPieRadius() + $this->getPieMargin();
			}
		} else {
			list($x, $y) = $this->getLegendPosition($i - 1);
			$x += $this->getLegendSpace() + $this->getLegendWidth($i - 1);
			if ($x + $this->getLegendSpace() + $this->getLegendWidth($i) > $this->getWidth()) {
				$x = $this->getLegendXPosition(0);
				$y += $this->getLegendSpace() + $this->getLegendHeight($i - 1);
			}
		}
		return array($x, $y);
	}

	private function getLegendXPosition($i) {
		list($x, $y) = $this->getLegendPosition($i);
		return $x;
	}

	private function getLegendYPosition($i) {
		list($x, $y) = $this->getLegendPosition($i);
		return $y;
	}

	private function getLegendWidth($i) {
		return $this->getLegendPreviewWidth() + $this->getLegendPreviewSpace() + ImageFontWidth($this->getLegendFont()) * strlen($this->getDataLegend($i));
	}

	private function getLegendHeight($i) {
		return max($this->getLegendPreviewHeight(), ImageFontHeight($this->getLegendFont()));
	}

	private function getLegendPreviewSpace() {
		return 3;
	}

	private function getLegendTextXPosition($i) {
		return $this->getLegendXPosition($i) + $this->getLegendPreviewWidth() + $this->getLegendPreviewSpace();
	}

	private function getLegendTextYPosition($i) {
		return $this->getLegendYPosition($i);
	}

	private function getLegendPreviewXPosition($i) {
		return $this->getLegendXPosition($i);
	}

	private function getLegendPreviewYPosition($i) {
		return $this->getLegendYPosition($i);
	}

	private function getLegendPreviewHeight() {
		return 14;
	}

	private function getLegendPreviewWidth() {
		return 14;
	}

	private function getLegendFont() {
		return 3;
	}

	public function plot($file = null) {
		$img = ImageCreate($this->getWidth(), $this->getHeight());
		$background	= ImageColorAllocate($img, 255,255,255);
		$legendcolor	= ImageColorAllocate($img,   0,  0,  0);

		$arcpos = 0;
		for ($i = 0; $i < $this->getDataCount(); $i++) {
			$dataColor = $this->getDataColor($i)->gdAllocate($img);

			// Draw Legend
			ImageFilledRectangle($img, $this->getLegendPreviewXPosition($i), $this->getLegendPreviewYPosition($i), $this->getLegendPreviewXPosition($i) + $this->getLegendPreviewWidth(), $this->getLegendPreviewYPosition($i) + $this->getLegendPreviewHeight(), $dataColor);
			ImageString($img, $this->getLegendFont(), $this->getLegendTextXPosition($i), $this->getLegendTextYPosition($i), iconv("UTF8", "ISO-8859-1", $this->getDataLegend($i)), $legendcolor);

			// Draw Arc
			$angle = round($this->getDataValue($i) / $this->getDataSum() * 360);
			if ($angle > 0) {
				ImageFilledArc($img, $this->getPieXPosition(), $this->getPieYPosition(), $this->getPieDiameter(), $this->getPieDiameter(), $arcpos, $arcpos + $angle, $dataColor, IMG_ARC_PIE);
				$arcpos += $angle;
			}
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

class Chart_Data {
	private $legend;
	private $value;
	private $color;

	public function __construct($legend, $value, $color = null) {
		$this->legend = $legend;
		$this->value = $value;
		$this->color = $color;
	}

	public function getLegend() {
		return $this->legend;
	}

	public function getValue() {
		return $this->value;
	}

	public function getColor() {
		return $this->color;
	}
}

?>
