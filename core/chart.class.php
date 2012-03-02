<?php

require_once(VPANEL_CORE . "/graph.class.php");

class Chart {
	private $width;
	private $height;
	private $data = array();
	private $minData = 0;
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

	protected function getMinData() {
		return $this->minData;
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

	protected function getDefaultDataColor($i) {
		$hash = hexdec(substr(md5($this->getDataLegend($i)),0,8));
		$r = $hash>>16 & 0xFF;
		$g = $hash >>8 & 0xFF;
		$b = $hash     & 0xFF;
		return new Graph_Color($r, $g, $b);
//		return new Graph_Color(rand(0,255), rand(0,255), rand(0,255));
	}

	protected function getDataColor($i) {
		$color = $this->data[$i]->getColor();
		if ($color == null) {
			$color = $this->getDefaultDataColor($i);
		}
		return $color;
	}

	public function addData($data) {
		$this->data[] = $data;
		$this->sumData += $data->getValue();
		$this->minData = min($this->minData, $data->getValue());
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

class VBarChart extends Chart {
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

	private function getDataXPosition($i) {
		return $this->getPlotXOffset() + (($i + 0.5) / $this->getDataCount()) * $this->getPlotWidth();
	}

	private function getBarWidth() {
		return max(4, $this->getPlotWidth() / $this->getDataCount() - $this->getBarSpace());
	}

	private function getBarSpace() {
		return 2;
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

	private function getAxisFont() {
		return 3;
	}

	protected function getDefaultDataColor($i) {
		return new Graph_Color(255, 0, 0);
	}

	public function plot($file) {
		$yaxis = new Graph_DefaultAxis($this->getMinData(), $this->getMaxData());
		$img = ImageCreate($this->getWidth(), $this->getHeight());
		$background	= ImageColorAllocate($img, 255,255,255);
		$axiscolor	= ImageColorAllocate($img,   0,  0,  0);

		// Y-Axis
		$posYaxis = 0;
		$lastYpixel_label = null;
		$lastYpixel_line = null;
		while ($yaxis->getLabelPosition($posYaxis) < 1 && $posYaxis < 30) {
			$labelPosition = $yaxis->getLabelPosition($posYaxis);
			$label = $yaxis->getLabel($labelPosition);
			$yPixel = $labelPosition * $this->getPlotHeight();
			$y = $this->getPlotYOffset() + ($this->getPlotHeight() - $yPixel);
			$preXpixel = ImageFontWidth($this->getAxisFont()) * strlen($label);
			$preYpixel = ImageFontHeight($this->getAxisFont()) / 2;
			$postYpixel = ImageFontHeight($this->getAxisFont()) / 2;

			if ($lastYpixel_label == null || $lastYpixel_label + 20 < $yPixel - $preYpixel) {
				ImageString($img, $this->getAxisFont(), $this->getPlotYAxisPosition() - $preXpixel, $y - $preYpixel, iconv("UTF8", "ISO-8859-1", $label), $axiscolor);
				$lastYpixel_label = $yPixel + $postYpixel;
			}
			if ($lastYpixel_line == null || $lastYpixel_line + 20 < $yPixel) {
				ImageLine($img, $this->getPlotXOffset(), $y, $this->getPlotXOffset() + $this->getPlotWidth(), $y, $axiscolor);
				$lastYpixel_line = $yPixel;
			}
			$posYaxis++;
		}

		// Data
		$lastXpixel_label = null;
		$y_null = $this->getPlotHeight() - $this->getPlotHeight() * $yaxis->getPosition(0);
		for ($i = 0; $i < $this->getDataCount(); $i++) {
			$dataColor = $this->getDataColor($i)->gdAllocate($img);
			$x = $this->getDataXPosition($i);
			$y = $this->getPlotHeight() - $this->getPlotHeight() * $yaxis->getPosition($this->getDataValue($i));

			for ($x_bar = $x - floor($this->getBarWidth() / 2); $x_bar <= $x + $this->getBarWidth() / 2; $x_bar++) {
				ImageLine($img, $x_bar, $y, $x_bar, $y_null, $dataColor);
			}

			// Draw Axis
			$label = $this->getDataLegend($i);
			$preXpixel = ImageFontWidth($this->getAxisFont()) * strlen($label) / 2;
			$postXpixel = ImageFontWidth($this->getAxisFont()) * strlen($label) / 2;
			if ($lastXpixel_label == null || $lastXpixel_label + 20 < $x - $preXpixel) {
				ImageString($img, $this->getAxisFont(), $x - $preXpixel, $this->getPlotXAxisPosition(), iconv("UTF8", "ISO-8859-1", $label), $axiscolor);
				$lastXpixel_label = $x + $postXpixel;
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
