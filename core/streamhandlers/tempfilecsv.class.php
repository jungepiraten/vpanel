<?php

require_once(VPANEL_STREAMHANDLERS . "/tempfile.class.php");

class CSVTempFileStreamHandler extends TempFileStreamHandler {
	private $handler;

	public function openFile($headers) {
		$file = $this->getFile();
		$file->setMimeType("text/csv");
		$file->setExportFilename($file->getExportFilename() . ".csv");
		$file->save();

		$this->handler = fopen($file->getAbsoluteFileName(), "w");
		$this->writeFile($headers);
	}

	public function writeFile($row) {
		fputcsv($this->handler, $row);
	}

	public function closeFile() {
		fclose($this->handler);
	}
}

?>
