<?php

require_once(VPANEL_CORE . "/dokumenttemplate.class.php");

class ReadBarcodeDokumentTemplate extends DokumentTemplate {
	private $fallback;

	private $file;
	private $barcode;
	private $data;

	public function __construct($templateid, $label, $permission, $gliederungid, $fallback) {
		parent::__construct($templateid, $label, $permission, $gliederungid);
		$this->fallback = $fallback;
	}

	public function getFallback() {
		return $this->fallback;
	}

	public function getBarcode($session) {
		if ($this->barcode === null) {
			$filename = $this->getDokumentFile()->getAbsoluteFilename();
			$codes = explode("\n", trim(shell_exec("zbarimg -Sdisable -Scode39.enable --raw -q -- " . escapeshellarg($filename))));
			$codes = array_filter($codes, create_function('$code', 'return substr($code, 0, 4) == "VP1-";'));
			if (count($codes) != 1) {
				$this->barcode = false;
			} else {
				$this->barcode = substr(array_shift($codes),3);
			}
		}
		return $this->barcode;
	}

	private function getBarcodeLink($barcode) {
		return "https://poststelle.junge-piraten.de/code.php?code=" . urlencode(barcode);
	}

	private function readBarcodeValue($session, $field, $fallbackFunction) {
		if ($this->data === null) {
			$barcode = $this->getBarcode($session);
			if ($barcode == false) {
				return call_user_func(array($this->fallback, $fallbackFunction), $session);
			}
			$this->data = json_decode(file_get_contents($this->getBarcodeLink($barcode)));
		}
		return $this->data->$field;
	}

	public function getDokumentFile($session) {
		if ($this->file === null) {
			$this->file = $session->getFileVariable("file");
		}
		return $this->file;
	}

	public function getDokumentKategorieID($session) {
		return $this->readBarcodeValue($session, "kategorieid", __FUNCTION__);
	}

	public function getDokumentStatusID($session) {
		return $this->readBarcodeValue($session, "statusid", __FUNCTION__);
	}

	public function getDokumentFlags($session) {
		return $this->readBarcodeValue($session, "flags", __FUNCTION__);
	}

	public function getDokumentIdentifier($session) {
		return $this->readBarcodeValue($session, "identifier", __FUNCTION__);
	}

	public function getDokumentLabel($session) {
		return $this->readBarcodeValue($session, "label", __FUNCTION__);
	}

	public function getDokumentData($session) {
		return $this->readBarcodeValue($session, "data", __FUNCTION__);
	}

	public function getDokumentKommentar($session) {
		return $this->readBarcodeValue($session, $field, __FUNCTION__);
	}
}

?>
