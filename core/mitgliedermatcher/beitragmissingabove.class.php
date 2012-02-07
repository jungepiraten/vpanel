<?php

require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

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
			$beitragmissing += $beitrag->getHoehe() - $beitrag->getBezahlt();
		}
		return $beitragmissing > $this->getBeitragMark();
	}
}

?>
