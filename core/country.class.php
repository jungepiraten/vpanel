<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class Country extends StorageClass {
	private $countryid;
	private $label;

	public static function factory(Storage $storage, $row) {
		$country = new Country($storage);
		$country->setCountryID($row["countryid"]);
		$country->setLabel($row["label"]);
		return $country;
	}

	public function getCountryID() {
		return $this->countryid;
	}

	public function setCountryID($countryid) {
		$this->countryid = $countryid;
	}

	public function getLabel() {
		return $this->label;
	}

	public function setLabel($label) {
		$this->label = $label;
	}

	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$this->setCountryID( $storage->setCountry(
			$this->getCountryID(),
			$this->getLabel() ));
	}
}

?>
