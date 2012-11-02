<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class DokumentNotify extends StorageClass {
	private $dokumentnotifyid;
	private $gliederungid;
	private $dokumentkategorieid;
	private $dokumentstatusid;
	private $emailid;

	private $gliederung;
	private $dokumentkategorie;
	private $dokumentstatus;
	private $email;

	public static function factory(Storage $storage, $row) {
		$dokumentnotify = new DokumentNotify($storage);
		$dokumentnotify->setGliederungID($row["gliederungid"]);
		$dokumentnotify->setDokumentNotifyID($row["dokumentnotifyid"]);
		$dokumentnotify->setDokumentKategorieID($row["dokumentkategorieid"]);
		$dokumentnotify->setDokumentStatusID($row["dokumentstatusid"]);
		$dokumentnotify->setEMailID($row["emailid"]);
		return $dokumentnotify;
	}

	public function getDokumentNotifyID() {
		return $this->dokumentnotifyid;
	}

	public function setDokumentNotifyID($dokumentnotifyid) {
		$this->dokumentnotifyid = $dokumentnotifyid;
	}

	public function getGliederungID() {
		return $this->gliederungid;
	}

	public function setGliederungID($gliederungid) {
		if ($gliederungid != $this->gliederungid) {
			$this->gliederung = null;
		}
		$this->gliederungid = $gliederungid;
	}

	public function getGliederung() {
		if ($this->gliederung == null) {
			$this->gliederung = $this->getStorage()->getGliederung($this->getGliederungID());
		}
		return $this->gliederung;
	}

	public function setGliederung($gliederung) {
		$this->setGliederungID($gliederung->getGliederungID());
		$this->gliederung = $gliederung;
	}

	public function getDokumentKategorieID() {
		return $this->dokumentkategorieid;
	}

	public function setDokumentKategorieID($dokumentkategorieid) {
		if ($dokumentkategorieid != $this->dokumentkategorieid) {
			$this->dokumentkategorie = null;
		}
		$this->dokumentkategorieid = $dokumentkategorieid;
	}

	public function getDokumentKategorie() {
		if ($this->dokumentkategorie == null) {
			$this->dokumentkategorie = $this->getStorage()->getDokumentKategorie($this->dokumentkategorieid);
		}
		return $this->dokumentkategorie;
	}

	public function setDokumentKategorie($dokumentkategorie) {
		$this->setDokumentKategorieID($dokumentkategorie->getDokumentKategorieID());
		$this->dokumentkategorie = $dokumentkategorie;
	}

	public function getDokumentStatusID() {
		return $this->dokumentstatusid;
	}

	public function setDokumentStatusID($dokumentstatusid) {
		if ($dokumentstatusid != $this->dokumentstatusid) {
			$this->dokumentstatus = null;
		}
		$this->dokumentstatusid = $dokumentstatusid;
	}

	public function getDokumentStatus() {
		if ($this->dokumentstatus == null) {
			$this->dokumentstatus = $this->getStorage()->getDokumentStatus($this->dokumentstatusid);
		}
		return $this->dokumentstatus;
	}

	public function setDokumentStatus($dokumentstatus) {
		$this->setDokumentStatusID($dokumentstatus->getDokumentStatusID());
		$this->dokumentstatus = $dokumentstatus;
	}

	public function getEMailID() {
		return $this->emailid;
	}

	public function setEMailID($emailid) {
		if ($this->emailid == $emailid) {
			$this->email = null;
		}
		$this->emailid = $emailid;
	}

	public function getEMail() {
		if ($this->email == null) {
			$this->email = $this->getStorage()->getEMail($this->emailid);
		}
		return $this->email;
	}

	public function setEMail($email) {
		$this->setEMailID($email->getEMailID());
		$this->email = $email;
	}

	public function save(Storage $storage = null) {
		if ($storage === null) {
			$storage = $this->getStorage();
		}
		$this->setDokumentNotifyID( $storage->setDokumentNotify(
			$this->getDokumentNotifyID(),
			$this->getGliederungID(),
			$this->getDokumentKategorieID(),
			$this->getDokumentStatusID(),
			$this->getEMailID() ));
	}

	public function notify($dokument, $notiz, $oldnotiz = null) {
		global $config;
		if ($this->getEMail() != null) {
			$mail = $config->createMail($this->getEMail());
			if ($oldnotiz == null) {
				$mail->setHeader("Subject", "[VPanel] Dokument " . $dokument->getLabel());
				$mail->setHeader("Message-ID", "dokumentnotify-" . $notiz->getDokumentNotizID() . "-" . $this->getEMail()->getEMailID() . "@" . $config->getHostPart());
				$mail->setBody(<<<EOT
Hallo,

bitte beachte das folgende Dokument:

Dokument ansehen:
{$config->getLink("dokumente_details", $dokument->getDokumentID())}

Gliederung:     {$dokument->getGliederung()->getLabel()}
Kategorie:      {$notiz->getNextKategorie()->getLabel()}
Status:         {$notiz->getNextStatus()->getLabel()}
Identifikation: {$notiz->getNextIdentifier()}
Titel:          {$notiz->getNextLabel()}
Kommentar:      {$notiz->getKommentar()}

Viele Grüße,

VPanel
EOT
);
			} else {
				$mail->setHeader("Subject", "[VPanel] [erledigt] Dokument " . $dokument->getLabel());
				$mail->setHeader("Message-ID", "dokumentnotify-" . $oldnotiz->getDokumentNotizID() . "-" . $notiz->getDokumentNotizID() . "-" . $this->getEMail()->getEMailID() . "@" . $config->getHostPart());
				$mail->setHeader("References", "dokumentnotify-" . $oldnotiz->getDokumentNotizID() . "-" . $this->getEMail()->getEMailID() . "@" . $config->getHostPart());
				$mail->setBody(<<<EOT
Hallo,

das Dokument wurde bearbeitet:

Dokument ansehen:
{$config->getLink("dokumente_details", $dokument->getDokumentID())}

Gliederung:     {$dokument->getGliederung()->getLabel()}
Kategorie:      {$notiz->getNextKategorie()->getLabel()}
Status:         {$notiz->getNextStatus()->getLabel()}
Identifikation: {$notiz->getNextIdentifier()}
Titel:          {$notiz->getNextLabel()}
Kommentar:      {$notiz->getKommentar()}

Viele Grüße,

VPanel
EOT
);
			}
			$mail->addAttachment($dokument->getFile());
			$config->getSendMailBackend()->send($mail);
		}
	}
}

?>
