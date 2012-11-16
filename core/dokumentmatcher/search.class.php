<?php

require_once(VPANEL_CORE . "/dokumentfilter.class.php");

class SearchDokumentMatcher extends DokumentMatcher {
	private $words;

	public function __construct($words) {
		if (!is_array($words)) {
			$words = explode(" ", $words);
		}
		$this->words = $words;
	}

	public function getWords() {
		return $this->words;
	}

	public function match(Dokument $dokument) {
		// TODO mir zu aufwändig :þ
		return false;
	}
}

?>
