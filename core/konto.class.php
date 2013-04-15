<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class Konto extends StorageClass {
	private $kontoid;
	private $inhaber;
	private $iban;
	private $bic;

	public static function factory(Storage $storage, $row) {
		$konto = new Konto($storage);
		$konto->setKontoID($row["kontoid"]);
		$konto->setInhaber($row["inhaber"]);
		$konto->setIBan($row["iban"]);
		$konto->setBIC($row["bic"]);
		return $konto;
	}

	public function getKontoID() {
		return $this->kontoid;
	}

	public function setKontoID($kontoid) {
		$this->kontoid = $kontoid;
	}

	public function getInhaber() {
		return $this->inhaber;
	}

	public function setInhaber($inhaber) {
		$this->inhaber = $inhaber;
	}

	public function getIBan() {
		return $this->iban;
	}

	public function setIBan($iban) {
		$this->iban = $iban;
	}

	public function getBIC() {
		return $this->bic;
	}

	public function setBIC($bic) {
		$this->bic = $bic;
	}

	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$this->setKontoID( $storage->setKonto(
			$this->getKontoID(),
			$this->getInhaber(),
			$this->getIBan(),
			$this->getBIC() ));
	}

	public function delete(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$storage->delKonto($this->getKontoID());
	}
}

?>
