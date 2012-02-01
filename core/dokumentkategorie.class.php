<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class DokumentKategorie extends StorageClass {
	private $dokumentkategorieid;
	private $label;

	public static function factory(Storage $storage, $row) {
		$dokumentkategorie = new DokumentKategorie($storage);
		$dokumentkategorie->setDokumentKategorieID($row["dokumentkategorieid"]);
		$dokumentkategorie->setLabel($row["label"]);
		return $dokumentkategorie;
	}

	public function getDokumentKategorieID() {
		return $this->dokumentkategorieid;
	}

	public function setDokumentKategorieID($dokumentkategorieid) {
		$this->dokumentkategorieid = $dokumentkategorieid;
	}

	public function getLabel() {
		return $this->label;
	}

	public function setLabel($label) {
		$this->label = $label;
	}

	public function save(Storage $storage = null) {
		if ($storage === null) {
			$storage = $this->getStorage();
		}
		$this->setDokumentKategorieID( $storage->setDokumentKategorie(
			$this->getDokumentKategorieID(),
			$this->getLabel() ));
	}
}

?>
