<?php

require_once(VPANEL_STREAMHANDLERS . "/tempfile.class.php");
require_once(VPANEL_LIBS . "/sepa.php");

class SepaTempFileStreamHandler extends TempFileStreamHandler {
	private $name;
	private $iban;
	private $bic;
	private $creditor_id;

	private $handler;
	private $payment;

	public static function factory(Storage $storage, $process, $row) {
		$handler = parent::factory($storage, $process, $row);
		$handler->setName($row["name"]);
		$handler->setIBan($row["iban"]);
		$handler->setBIC($row["bic"]);
		$handler->setCreditorId($row["creditor_id"]);
		return $handler;
	}

	public function getData() {
		$data = parent::getData();
		$data["name"] = $this->name;
		$data["iban"] = $this->iban;
		$data["bic"] = $this->bic;
		$data["creditor_id"] = $this->creditor_id;
		return $data;
	}

	public function __construct($name = null, $iban = null, $bic = null, $creditor_id = null) {
		$this->name = $name;
		$this->iban = $iban;
		$this->bic = $bic;
		$this->creditor_id = $creditor_id;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function setIBan($iban) {
		$this->iban = $iban;
	}

	public function setBIC($bic) {
		$this->bic = $bic;
	}

	public function setCreditorId($creditor_id) {
		$this->creditor_id = $creditor_id;
	}

	public function openFile($headers) {
		$this->handler = new EBICS_Sepa($this->name);
		$this->handler->setPaymentInformation(array(
			"name" => $this->name,
			"iban" => $this->iban,
			"bic" => $this->bic,
			"glaeubiger_id" => $this->creditor_id,
		));
	}

	public function writeFile($row) {
		$this->handler->addTransaction(array(
			"name" => $row["kontoinhaber"],
			"iban" => $row["iban"],
			"bic" => $row["bic"],
		), array(
			"id" => "MITGLIED-" . $row["mitgliedid"],
			"datum" => date("Y-m-d"),
		), $row["betrag"], "Danke " . $row["beitrag"] . " #" . $row["mitgliedid"]);
	}

	public function closeFile() {
		$file = $this->getFile();
		$file->setMimeType("application/octet-stream");
		$file->setExportFilename($file->getExportFilename() . ".xml");
		$file->save();

		$this->handler->saveFile($this->getFile()->getAbsoluteFileName());
	}
}

?>
