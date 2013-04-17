<?php

require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

abstract class EMailBounceCountMitgliederMatcher extends MitgliederMatcher {
	private $countlimit;
	private $countold;

	public function __construct($countlimit, $countold = false) {
		$this->countlimit = $countlimit;
		$this->countold = $countold;
	}

	public function getCountLimit() {
		return $this->countlimit;
	}

	public function countOldBounces() {
		return $this->countold;
	}
}

class EMailBounceCountAboveMitgliederMatcher extends EMailBounceCountMitgliederMatcher {
	public function match(Mitglied $mitglied) {
		return count($mitglied->getLatestRevision()->getKontakt()->getEMail()->getBounces()) > $this->getCountLimit();
	}
}

class EMailBounceCountBelowMitgliederMatcher extends EMailBounceCountMitgliederMatcher {
	public function match(Mitglied $mitglied) {
		return count($mitglied->getLatestRevision()->getKontakt()->getEMail()->getBounces()) <= $this->getCountLimit();
	}
}

?>
