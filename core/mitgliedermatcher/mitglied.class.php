<?php

require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

class MitgliedMitgliederMatcher extends MitgliederMatcher {
	private $mitgliedid;

	public function __construct($mitgliedid) {
		$this->mitgliedid = $mitgliedid;
	}

	public function getMitgliedID() {
		return $this->mitgliedid;
	}

	public function match(Mitglied $mitglied) {
		return $mitglied->getMitgliedID() == $this->getMitgliedID();
	}
}

?>
