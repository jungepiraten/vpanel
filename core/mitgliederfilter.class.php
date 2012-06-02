<?php

require_once(VPANEL_CORE . "/aktion.class.php");
require_once(VPANEL_MITGLIEDERMATCHER . "/logic.class.php");
require_once(VPANEL_MITGLIEDERMATCHER . "/gliederung.class.php");

class MitgliederFilter extends Aktion {
	private $filterid;
	private $matcher;

	public function __construct($filterid, $label, $permission, $gliederungid, $matcher) {
		parent::__construct($label, $permission, $gliederungid);
		$this->filterid = $filterid;
		if ($gliederungid != null) {
			$matcher = new AndMitgliederMatcher(new GliederungMitgliederMatcher($gliederungid), $matcher);
		}
		$this->matcher = $matcher;
	}
	
	public function getFilterID() {
		return $this->filterid;
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
