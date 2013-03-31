<?php

require_once(VPANEL_LIBS . "/fpdf/fpdf.php");
require_once(VPANEL_LIBS . "/fpdf/fpdi.php");
require_once(VPANEL_PROCESSES . "/dokumenttransition.class.php");

class DokumentTransaktionDownloadProcess extends DokumentTransitionProcess {
	private $tempfile;
	private $ziphandler;

	public static function factory(Storage $storage, $row) {
		$process = parent::factory($storage, $row);
		if (isset($row["tempfileid"])) {
			$process->setTempFile($storage->getTempFile($row["tempfileid"]));
		}
		return $process;
	}

	public function setTempFile($tempfile) {
		$this->tempfile = $tempfile;
	}

	public function getTempFile() {
		if ($this->tempfile == null) {
			$this->tempfile = new TempFile($this->getStorage());
			$file = new File($this->getStorage());
			$file->setMimeType("application/zip");
			$file->setExportFilename("download.zip");
			$file->save();
			$this->tempfile->setUserID($this->getUserID());
			$this->tempfile->setFile($file);
			$this->tempfile->setTimestamp(time());
			$this->tempfile->save();
		}
		return $this->tempfile;
	}

	public function getData() {
		$data = parent::getData();
		$data["tempfileid"] = $this->getTempFile()->getTempFileID();
		return $data;
	}

	public function initProcess() {
		$this->ziphandler = new ZipArchive;
		$this->ziphandler->open($this->getTempFile()->getFile()->getAbsoluteFilename(), ZipArchive::CREATE);
	}

	public function runProcessStep($dokument) {
		$file = $dokument->getLatestRevision()->getFile();
		$extension = array_pop(explode(".", $file->getExportFilename()));
		#$this->ziphandler->addFile($file->getAbsoluteFilename(), $file->getExportFilename());

		// Print Metainfo for PDFs
		if (strtolower($extension) == "pdf") {
			try {
				$fpdf =& new FPDI();
				$pagecount = $fpdf->setSourceFile($file->getAbsoluteFilename());
				$fpdf->SetMargins(0,0,0);
				$fpdf->SetFont('Courier','',8);
				$fpdf->SetTextColor(0,0,0);
				$documentSize = null;
				for ($i=0;$i<$pagecount;$i++) {
					$string = $dokument->getLatestRevision()->getIdentifier() . " (Seite " . ($i+1) . " von " . $pagecount . ", Revision " . $dokument->getLatestRevision()->getRevisionID() . ")";
					$fpdf->AddPage();
					$tpl = $fpdf->importPage($i + 1);
					$size = $fpdf->getTemplateSize($tpl);
					// First Page defines documentSize
					if ($documentSize === null) {
						$documentSize = $size;
					}

					// Center Template on Document
					$fpdf->useTemplate($tpl,
						intval(($documentSize["w"] - $size["w"]) / 2),
						intval(($documentSize["h"] - $size["h"]) / 2),
						0, 0, true);
					$fpdf->Text(intval($documentSize["w"]) - 10 - $fpdf->GetStringWidth($string), 5, $string);
				}
				$this->ziphandler->addFromString($dokument->getLatestRevision()->getIdentifier() . "." . $extension, $fpdf->Output("", "S"));
			} catch (Exception $e) {
				$this->ziphandler->addFile($file->getAbsoluteFilename(), $dokument->getLatestRevision()->getIdentifier() . "." . $extension);
			}
		} else {
			$this->ziphandler->addFile($file->getAbsoluteFilename(), $dokument->getLatestRevision()->getIdentifier() . "." . $extension);
		}
	}

	public function finalizeProcess() {
		$this->ziphandler->close();
	}

	public function delete() {
		if ($this->tempfile != null) {
			$this->tempfile->delete();
		}
		parent::delete();
	}
}

?>
