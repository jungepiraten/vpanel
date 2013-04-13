<?php

require_once(VPANEL_STREAMHANDLERS . "/tempfile.class.php");
require_once(VPANEL_LIBS . "/phpDTAUS.php");

class DTAUSTempFileStreamHandler extends TempFileStreamHandler {
	private $name;
	private $bankcode;
	private $account;

	private $handler;

	public static function factory(Storage $storage, $process, $row) {
		$handler = parent::factory($storage, $process, $row);
		$handler->setName($row["name"]);
		$handler->setBankCode($row["bankcode"]);
		$handler->setAccount($row["account"]);
		return $handler;
	}

	public function getData() {
		$data = parent::getData();
		$data["name"] = $this->name;
		$data["bankcode"] = $this->bankcode;
		$data["account"] = $this->account;
		return $data;
	}

	public function __construct($name = null, $bankcode = null, $account = null) {
		$this->name = $name;
		$this->bankcode = $bankcode;
		$this->account = $account;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function setBankCode($bankcode) {
		$this->bankcode = $bankcode;
	}

	public function setAccount($account) {
		$this->account = $account;
	}

	public function openFile($headers) {
		$file = $this->getFile();
		$file->setMimeType("application/octet-stream");
		$file->setExportFilename($file->getExportFilename() . ".dtaus");
		$file->save();

		$this->handler = new phpDTAUS($this->name, $this->bankcode, $this->account);
	}

	public function writeFile($row) {
		$iban = $row["iban"];
		if (substr(strtoupper($iban),0,2) == "DE") {
			$bankcode = substr($iban, 4, 8);
			$account = substr($iban, 12);
			$this->handler->addTransaction($row["mitglied"], $bankcode, $account, $row["betrag"], $row["beitrag"] . " #" . $row["mitgliedid"]);
		}
	}

	public function closeFile() {
		file_put_contents($this->getFile()->getAbsoluteFileName(), $this->handler->createDtaus());
	}
}

?>
