<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class DokumentNotify extends StorageClass {
	private $dokumentnotifyid;
	private $dokumentkategorieid;
	private $dokumentstatusid;
	private $mail;

	private $dokumentkategorie;
	private $dokumentstatus;

	public static function factory(Storage $storage, $row) {
		$dokumentnotify = new DokumentNotify($storage);
		$dokumentnotify->setDokumentNotifyID($row["dokumentnotifyid"]);
		$dokumentnotify->setDokumentKategorieID($row["dokumentkategorieid"]);
		$dokumentnotify->setDokumentStatusID($row["dokumentstatusid"]);
		$dokumentnotify->setMail($row["mail"]);
		return $dokumentnotify;
	}

	public function getDokumentNotifyID() {
		return $this->dokumentnotifyid;
	}

	public function setDokumentNotifyID($dokumentnotifyid) {
		$this->dokumentnotifyid = $dokumentnotifyid;
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

	public function getMail() {
		return $this->mail;
	}

	public function setMail($mail) {
		$this->mail = $mail;
	}

	public function save(Storage $storage = null) {
		if ($storage === null) {
			$storage = $this->getStorage();
		}
		$this->setDokumentNotifyID( $storage->setDokumentNotify(
			$this->getDokumentNotifyID(),
			$this->getDokumentKategorieID(),
			$this->getDokumentStatusID(),
			$this->getMail() ));
	}

	public function notify($dokument, $notiz) {
		global $config;
		if ($this->getMail() != null) {
			$mail = $config->createMail();
			$mail->setHeader("Subject", "[VPanel] Dokument");
			$mail->setBody(<<<EOT
Hallo,

bitte beachte das Dokument {$dokument->getLabel()}:

Kategorie: {$dokument->getDokumentKategorie()->getLabel()}
Status:    {$dokument->getDokumentStatus()->getLabel()}

{$notiz->getKommentar()}

Viele Grüße,

VPanel
EOT
);
			$mail->setRecipient($this->getMail());
			$config->getSendMailBackend()->send($mail);
		}
	}
}

?>
