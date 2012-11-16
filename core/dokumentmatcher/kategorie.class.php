<?php

require_once(VPANEL_CORE . "/dokumentfilter.class.php");

class KategorieDokumentMatcher extends DokumentMatcher {
	private $kategorieid;

	public function __construct($kategorie) {
		if ($kategorie instanceof DokumentKategorie) {
			$kategorie = $kategorie->getDokumentKategorieID();
		}
		$this->kategorieid = $kategorie;
	}

	public function getKategorieID() {
		return $this->kategorieid;
	}

	public function match(Dokument $dokument) {
		return $dokument->getDokumentKategorieID() == $this->getKategorieID();
	}
}

?>
