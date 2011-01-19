<?php

require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

class StateMitgliederMatcher extends MitgliederMatcher {
	private $stateid;
	
	public function __construct($state) {
		if ($state instanceof State) {
			$this->stateid = $state->getStateID();
		} else {
			$this->stateid = $state;
		}
	}

	public function getStateID() {
		return $this->stateid;
	}

	public function match(Mitglied $mitglied) {
		return $mitglied->getLatestRevision()->getKontakt()->getOrt()->getStateID() == $this->stateid;
	}
}

?>
