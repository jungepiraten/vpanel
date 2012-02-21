<?php

require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

class EMailBounceCountAboveMitgliederMatcher extends MitgliederMatcher {
	private $countlimit;

	public __construct($countlimit) {
		$this->countlimit = $countlimit;
	}

	public function getCountLimit() {
		return $this->countlimit;
	}

	public function match(Mitglied $mitglied) {
		return $mitglied->getLatestRevision()->getKontakt()->getEMail()->getBeitragCount() > $this->getCountLimit();
	}
}

class EMailBounceCountBelowMitgliederMatcher extends MitgliederMatcher {
	private $countlimit;

	public __construct($countlimit) {
		$this->countlimit = $countlimit;
	}

	public function getCountLimit() {
		return $this->countlimit;
	}

	public function match(Mitglied $mitglied) {
		return $mitglied->getLatestRevision()->getKontakt()->getEMail()->getBeitragCount() <= $this->getCountLimit();
	}
}

?>
