<?php

require_once(VPANEL_STREAMHANDLERS . "/tempfile.class.php");
require_once(VPANEL_LIBS . "/php-sepa-xml/SepaTransferFile.php");

class SepaTempFileStreamHandler extends TempFileStreamHandler {
	private $name;
	private $iban;
	private $bic;

	private $handler;

	public static function factory(Storage $storage, $process, $row) {
		$handler = parent::factory($storage, $process, $row);
		$handler->setName($row["name"]);
		$handler->setIBan($row["iban"]);
		$handler->setBIC($row["bic"]);
		return $handler;
	}

	public function getData() {
		$data = parent::getData();
		$data["name"] = $this->name;
		$data["iban"] = $this->iban;
		$data["bic"] = $this->bic;
		return $data;
	}

	public function __construct($name = null, $iban = null, $bic = null) {
		$this->name = $name;
		$this->iban = $iban;
		$this->bic = $bic;
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

	public function openFile($headers) {
		$this->handler = new SepaTransferFile();
		$this->handler->isTest = true;
		$this->handler->messageIdentification = uniqid();
		$this->handler->initiatingPartyName = $this->name;

		$this->handler->addPaymentInfo(array(
			"id"			=> uniqid(),
			"debtorName"		=> $this->name,
			"debtorAccountIBAN"	=> $this->iban,
			"debtorAgentBIC"	=> $this->bic
		));
	}

	public function writeFile($row) {
		$this->handler->addCreditTransfer(array(
			"id"			=> $row["beitrag"] . " #" . $row["mitgliedid"],
			"currency"		=> "EUR",
			"amount"		=> $row["betrag"],
			"creditorBIC"		=> $row["bic"],
			"creditorName"		=> $row["kontoinhaber"],
			"creditorAccountIBAN"	=> $row["iban"],
			"remittanceInformation"	=> "Mitglied" . $row["mitgliedid"],
		));
	}

	public function closeFile() {
		$file = $this->getFile();
		$file->setMimeType("application/octet-stream");
		$file->setExportFilename($file->getExportFilename() . ".xml");
		$file->save();

		file_put_contents($this->getFile()->getAbsoluteFileName(), $this->handler->asXML());
	}
}

?>
