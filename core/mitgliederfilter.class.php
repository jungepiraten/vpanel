<?php

require_once(VPANEL_MITGLIEDERMATCHER . "/logic.class.php");
require_once(VPANEL_MITGLIEDERMATCHER . "/gliederung.class.php");

class MitgliederFilter {
	private $filterid;
	private $label;
	private $matcher;
	private $gliederungid;

	public function __construct($filterid, $label, $gliederungid, $matcher) {
		$this->filterid = $filterid;
		$this->label = $label;
		$this->gliederungid = $gliederungid;
		if ($gliederungid != null) {
			$matcher = new AndMitgliederMatcher(new GliederungMitgliederMatcher($gliederungid), $matcher);
		}
		$this->matcher = $matcher;
	}
	
	public function getFilterID() {
		return $this->filterid;
	}
	
	public function getLabel() {
		return $this->label;
	}
	
	public function getGliederungID() {
		return $this->gliederungid;
	}
	
	public function getMatcher() {
		return $this->matcher;
	}

	public function match($mitglied) {
		return $this->matcher->match($mitglied);
	}
}

abstract class MitgliederMatcher {
	abstract public function match(Mitglied $mitglied);
}

?>
