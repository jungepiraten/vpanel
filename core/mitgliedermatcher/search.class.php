<?php

require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

class SearchMitgliederMatcher extends MitgliederMatcher {
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

	public function match(Mitglied $mitglied) {
		// TODO mir zu aufwändig :þ
		return false;
		//return $mitglied->getLatestRevision()->getKontakt()->getOrt()->getStateID() == $this->stateid;
	}
}

?>
