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
		return $beitrag->getRemaining() <= 0;
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
		$beitragpaid = 0;
		foreach ($mitglied->getBeitragList() as $beitrag) {
			$beitragpaid += $beitrag->getBuchungenHoehe();
		}
		return $beitragpaid > $this->getBeitragMark();
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
		$beitragpaid = 0;
		foreach ($mitglied->getBeitragList() as $beitrag) {
			$beitragpaid += $beitrag->getBuchungenHoehe();
		}
		return $beitragpaid <= $this->getBeitragMark();
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
		$beitrag = $mitglied->getBeitrag($this->getBeitragID());
		if ($beitrag == null) {
			return false;
		}
		return $beitrag->getRemaining() > 0;
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
		$beitragmissing = 0;
		foreach ($mitglied->getBeitragList() as $beitrag) {
			$beitragmissing += abs($beitrag->getRemaining());
		}
		return $beitragmissing > $this->getBeitragMark();
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
		$beitragmissing = 0;
		foreach ($mitglied->getBeitragList() as $beitrag) {
			$beitragmissing += abs($beitrag->getRemaining());
		}
		return $beitragmissing <= $this->getBeitragMark();
	}
}

?>
