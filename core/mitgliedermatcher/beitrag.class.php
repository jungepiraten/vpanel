<?php

require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

class BeitragMitgliederMatcher extends MitgliederMatcher {
	private $beitragid;

	public function __construct($beitragid) {
		$this->beitragid = $beitragid;
	}

	public function getBeitragID() {
		return $this->beitragid;
	}

	public function match(Mitglied $mitglied) {
		$beitrag = $mitglied->getBeitrag($this->getBeitragID());
		if ($beitrag == null) {
			return false;
		}
		return true;
	}
}

/**
 * BeitragPaid
 **/
class BeitragPaidMitgliederMatcher extends MitgliederMatcher {
	private $beitragid;

	public function __construct($beitragid) {
		$this->beitragid = $beitragid;
	}

	public function getBeitragID() {
		return $this->beitragid;
	}

	public function match(Mitglied $mitglied) {
		$beitrag = $mitglied->getBeitrag($this->getBeitragID());
		if ($beitrag == null) {
			return false;
		}
		return $beitrag->getRemainingHoehe() <= 0;
	}
}

class BeitragPaidAboveMitgliederMatcher extends MitgliederMatcher {
	private $beitragmark;

	public function __construct($beitragmark = 0) {
		$this->beitragmark = $beitragmark;
	}

	public function getBeitragMark() {
		return $this->beitragmark;
	}

	public function match(Mitglied $mitglied) {
		return $mitglied->getPaidBeitrag() > $this->getBeitragMark();
	}
}

class BeitragPaidBelowMitgliederMatcher extends MitgliederMatcher {
	private $beitragmark;

	public function __construct($beitragmark = 0) {
		$this->beitragmark = $beitragmark;
	}

	public function getBeitragMark() {
		return $this->beitragmark;
	}

	public function match(Mitglied $mitglied) {
		return $mitglied->getPaidBeitrag() <= $this->getBeitragMark();
	}
}

/**
 * BeitragMissing
 **/
class BeitragMissingMitgliederMatcher extends MitgliederMatcher {
	private $beitragid;

	public function __construct($beitragid) {
		$this->beitragid = $beitragid;
	}

	public function getBeitragID() {
		return $this->beitragid;
	}

	public function match(Mitglied $mitglied) {
		return $mitglied->getSchulden() > 0;
	}
}

class BeitragMissingAboveMitgliederMatcher extends MitgliederMatcher {
	private $beitragmark;

	public function __construct($beitragmark = 0) {
		$this->beitragmark = $beitragmark;
	}

	public function getBeitragMark() {
		return $this->beitragmark;
	}

	public function match(Mitglied $mitglied) {
		return $mitglied->getSchulden() > $this->getBeitragMark();
	}
}

class BeitragMissingBelowMitgliederMatcher extends MitgliederMatcher {
	private $beitragmark;

	public function __construct($beitragmark = 0) {
		$this->beitragmark = $beitragmark;
	}

	public function getBeitragMark() {
		return $this->beitragmark;
	}

	public function match(Mitglied $mitglied) {
		return $mitglied->getSchulden() <= $this->getBeitragMark();
	}
}

?>
