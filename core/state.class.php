<?php

require_once(VPANEL_CORE . "/storageobject.class.php");
require_once(VPANEL_CORE . "/country.class.php");
require_once(VPANEL_CORE . "/mitgliedermatcher/logic.class.php");
require_once(VPANEL_CORE . "/mitgliedermatcher/state.class.php");

class State extends StorageClass {
	private $stateid;
	private $label;
	private $countryid;

	private $country;

	public static function factory(Storage $storage, $row) {
		$state = new State($storage);
		$state->setStateID($row["stateid"]);
		$state->setLabel($row["label"]);
		$state->setPopulation($row["population"]);
		$state->setCountryID($row["countryid"]);
		return $state;
	}

	public function getStateID() {
		return $this->stateid;
	}

	public function setStateID($stateid) {
		$this->stateid = $stateid;
	}

	public function getLabel() {
		return $this->label;
	}

	public function setLabel($label) {
		$this->label = $label;
	}

	public function getPopulation() {
		return $this->population;
	}

	public function setPopulation($population) {
		$this->population = $population;
	}

	public function getCountry() {
		if ($this->country == null) {
			$this->country = $this->getStorage()->getCountry($this->getCountryID());
		}
		return $this->country;
	}

	public function getCountryID() {
		return $this->countryid;
	}

	public function setCountry(Country $country) {
		$this->setCountryID($country->getCountryID());
		$this->country = $country;
	}

	public function setCountryID($countryid) {
		if ($this->countryid != $countryid) {
			$this->country = null;
		}
		$this->countryid = $countryid;
	}

	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$storage->setState(
			$this->getStateID(),
			$this->getLabel(),
			$this->getPopulation(),
			$this->getCountryID() );
	}

	public function getMitgliederCount() {
		return $this->getStorage()->getMitgliederCount(new AndMitgliederMatcher(	new StateMitgliederMatcher($this->getStateID()),
												new NotMitgliederMatcher(new AusgetretenMitgliederMatcher()) ));
	}
}

?>
