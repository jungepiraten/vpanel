<?php

require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

abstract class EMailBounceCountMitgliederMatcher extends MitgliederMatcher {
	private $countlimit;
	private $countold;

	public function __construct($countlimit, $countold = false) {
		$this->countlimit = $countlimit;
		$this->countold = $countold;
	}

	protected function getBounceCount(Mitglied $mitglied) {
		if ($this->countOldBounces()) {
			$bounces = $mitglied->getLatestRevision()->getKontakt()->getEMail()->getBounces();
		} else {
			$bounces = $mitglied->getLatestRevision()->getKontakt()->getEMail()->getNewBounces();
		}
		return count($bounces);
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
		return $this->getBounceCount($mitglied) > $this->getCountLimit();
	}
}

class EMailBounceCountBelowMitgliederMatcher extends EMailBounceCountMitgliederMatcher {
	public function match(Mitglied $mitglied) {
		return $this->getBounceCount($mitglied) <= $this->getCountLimit();
	}
}

?>
