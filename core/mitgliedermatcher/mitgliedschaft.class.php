<?php

require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

class MitgliedschaftMitgliederMatcher extends MitgliederMatcher {
	private $mitgliedschaftid;
	
	public function __construct($mitgliedschaft) {
		if ($mitgliedschaft instanceof Mitgliedschaft) {
			$this->mitgliedschaftid = $mitgliedschaft->getMitgliedschaftID();
		} else {
			$this->mitgliedschaftid = $mitgliedschaft;
		}
	}

	public function getMitgliedschaftID() {
		return $this->mitgliedschaftid;
	}

	public function match(Mitglied $mitglied) {
		return $mitglied->getLatestRevision()->getMitgliedschaftID() == $this->mitgliedschaftid;
	}
}

?>
