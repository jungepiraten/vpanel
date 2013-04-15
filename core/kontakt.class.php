<?php

require_once(VPANEL_CORE . "/globalobject.class.php");
require_once(VPANEL_CORE . "/email.class.php");
require_once(VPANEL_CORE . "/ort.class.php");

class Kontakt extends GlobalClass {
	private $kontaktid;
	private $adresszusatz;
	private $strasse;
	private $hausnummer;
	private $ortid;
	private $telefonnummer;
	private $handynummer;
	private $emailid;
	private $kontoid;

	private $ort;
	private $email;
	private $konto;

	public static function factory(Storage $storage, $row) {
		$kontakt = new Kontakt($storage);
		$kontakt->setKontaktID($row["kontaktid"]);
		$kontakt->setAdresszusatz($row["adresszusatz"]);
		$kontakt->setStrasse($row["strasse"]);
		$kontakt->setHausnummer($row["hausnummer"]);
		$kontakt->setOrtID($row["ortid"]);
		$kontakt->setTelefonnummer($row["telefonnummer"]);
		$kontakt->setHandynummer($row["handynummer"]);
		$kontakt->setEMailID($row["emailid"]);
		$kontakt->setKontoID($row["kontoid"]);
		return $kontakt;
	}

	public function getKontaktID() {
		return $this->kontaktid;
	}

	public function setKontaktID($kontaktid) {
		$this->kontaktid = $kontaktid;
	}

	public function getAdresszusatz() {
		return $this->adresszusatz;
	}

	public function setAdresszusatz($adresszusatz) {
		$this->adresszusatz = $adresszusatz;
	}

	public function getStrasse() {
		return $this->strasse;
	}

	public function setStrasse($strasse) {
		$this->strasse = $strasse;
	}

	public function getHausnummer() {
		return $this->hausnummer;
	}

	public function setHausnummer($hausnummer) {
		$this->hausnummer = $hausnummer;
	}

	public function getOrt() {
		if ($this->ort == null) {
			$this->ort = $this->getStorage()->getOrt($this->getOrtID());
		}
		return $this->ort;
	}

	public function getOrtID() {
		return $this->ortid;
	}

	public function setOrt(Ort $ort) {
		$this->setOrtID($ort->getOrtID());
		$this->ort = $ort;
	}

	public function setOrtID($ortid) {
		if ($ortid != $this->ortid) {
			$this->ort = null;
		}
		$this->ortid = $ortid;
	}

	public function getTelefonnummer() {
		return $this->telefonnummer;
	}

	public function setTelefonnummer($telefonnummer) {
		$this->telefonnummer = $telefonnummer;
	}

	public function getHandynummer() {
		return $this->handynummer;
	}

	public function setHandynummer($handynummer) {
		$this->handynummer = $handynummer;
	}

	public function getEMailID() {
		return $this->emailid;
	}

	public function setEMailID($emailid) {
		if ($this->emailid != $emailid) {
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

	public function getKontoID() {
		return $this->kontoid;
	}

	public function setKontoID($kontoid) {
		if ($this->kontoid != $kontoid) {
			$this->konto = null;
		}
		$this->kontoid = $kontoid;
	}

	public function getKonto() {
		if ($this->konto == null) {
			$this->konto = $this->getStorage()->getKonto($this->kontoid);
		}
		return $this->konto;
	}

	public function setKonto($konto) {
		$this->setKontoID($konto == null ? null : $konto->getKontoID());
		$this->konto = $konto;
	}

	public function hasKonto() {
		return $this->kontoid != null;
	}

	public function save(Storage $storage = null) {
		if ($storage === null) {
			$storage = $this->getStorage();
		}
		var_dump($this->getKontoID());
		$this->setKontaktID( $storage->setKontakt(
			$this->getKontaktID(),
			$this->getAdresszusatz(),
			$this->getStrasse(),
			$this->getHausnummer(),
			$this->getOrtID(),
			$this->getTelefonnummer(),
			$this->getHandynummer(),
			$this->getEMailID(),
			$this->getKontoID() ));
	}
}

?>
