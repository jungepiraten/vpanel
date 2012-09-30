<?php

require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

class NatPersonAgeMitgliederMatcher extends MitgliederMatcher {
	private $age;
	
	public function __construct($age) {
		$this->age = $age;
	}
	
	public function getAge() {
		return $this->age;
	}
	
	public function match(Mitglied $mitglied) {
		return $mitglied->getLatestRevision()->isNatPerson() && time() - $mitglied->getLatestRevision()->getGeburtsdatum() > $age*356*24*60*60;
	}
}

class EintrittAgeNatPersonMitgliederMatcher extends NatPersonAgeMitgliederMatcher {
	public function match(Mitglied $mitglied) {
		return $mitglied->getLatestRevision()->isNatPerson() && $mitglied->getEintrittsdatum() - $mitglied->getLatestRevision()->getGeburtsdatum() > $age*356*24*60*60;
	}
}

?>
