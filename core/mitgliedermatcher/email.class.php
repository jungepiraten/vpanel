<?php

require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

class EMailBounceCountAboveMitgliederMatcher extends MitgliederMatcher {
	private $countlimit;

	public function __construct($countlimit) {
		$this->countlimit = $countlimit;
	}

	public function getCountLimit() {
		return $this->countlimit;
	}

	public function match(Mitglied $mitglied) {
		return count($mitglied->getLatestRevision()->getKontakt()->getEMail()->getBounces()) > $this->getCountLimit();
	}
}

class EMailBounceCountBelowMitgliederMatcher extends MitgliederMatcher {
	private $countlimit;

	public function __construct($countlimit) {
		$this->countlimit = $countlimit;
	}

	public function getCountLimit() {
		return $this->countlimit;
	}

	public function match(Mitglied $mitglied) {
		return count($mitglied->getLatestRevision()->getKontakt()->getEMail()->getBounces()) <= $this->getCountLimit();
	}
}

?>
