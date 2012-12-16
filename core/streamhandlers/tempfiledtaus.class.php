<?php

require_once(VPANEL_STREAMHANDLERS . "/tempfile.class.php");
require_once(VPANEL_LIBS . "/phpDTAUS.php");

class DTAUSTempFileStreamHandler extends TempFileStreamHandler {
	private $name;
	private $bankcode;
	private $account;

	private $handler;

	public function __construct($name, $bankcode, $account)  {
		$this->name = $name;
		$this->bankcode = $bankcode;
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
		if (substr($iban,0,2) == "DE") {
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
