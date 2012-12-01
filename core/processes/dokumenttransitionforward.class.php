<?php

require_once(VPANEL_PROCESSES . "/dokumenttransition.class.php");

class DokumentTransaktionForwardProcess extends DokumentTransitionProcess {
	private $forwardemailid;

	public static function factory(Storage $storage, $row) {
		$process = parent::factory($storage, $row);
		$process->setForwardEMailID($row["forwardemailid"]);
		return $process;
	}

	public function getData() {
		$data = parent::getData();
		$data["forwardemailid"] = $this->getForwardEMailID();
		return $data;
	}

	public function getForwardEMailID() {
		return $this->forwardemailid;
	}

	public function setForwardEMailID($forwardemailid) {
		if ($forwardemailid != $this->forwardemailid) {
			$this->forwardemail = null;
		}
		$this->forwardemailid = $forwardemailid;
	}

	public function getForwardEMail() {
		if ($this->forwardemail == null) {
			$this->forwardemail = $this->getStorage()->getEMail($this->forwardemailid);
		}
		return $this->forwardemail;
	}

	public function setForwardEMail($forwardemail) {
		$this->setForwardEMailID($forwardemail->getEMailID());
		$this->forwardemail = $forwardemail;
	}

	public function getNotizKommentar() {
		return sprintf(parent::getNotizKommentar(), $this->getForwardEMail()->getEMail());
	}

	public function initProcess() {
	}

	public function runProcessStep($dokument) {
		global $config;
		$mail = $config->createMail($this->getForwardEMail());
		$mail->setHeader("Subject", "Eingehendes Dokument");
		$mail->setHeader("MessageID", "<dokumentforward-" . $this->getForwardEMailID() . "-" . $dokument->getDokumentID() . "-" . microtime(true) . "@" . $config->getHostPart() . ">");
		$mail->setBody(<<<EOT
Hallo,

anbei ein Dokument und Details dazu:

Gliederung:     {$dokument->getGliederung()->getLabel()}
Kategorie:      {$dokument->getDokumentKategorie()->getLabel()}
Status:         {$dokument->getDokumentStatus()->getLabel()}
Identifikation: {$dokument->getIdentifier()}
Titel:          {$dokument->getLabel()}

Viele Grüße,

VPanel
EOT
);
		$mail->addAttachment($dokument->getFile());
		$config->getSendMailBackend()->send($mail);
	}

	public function finalizeProcess() {
	}
}

?>
