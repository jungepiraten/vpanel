<?php

require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

class OrtMitgliederMatcher extends MitgliederMatcher {
	private $ortid;

	public function __construct($ort) {
		if ($ort instanceof Ort) {
			$this->ortid = $ort->getOrtID();
		} else {
			$this->ortid = $ort;
		}
	}

	public function getOrtID() {
		return $this->ortid;
	}

	public function match(Mitglied $mitglied) {
		return $mitglied->getLatestRevision()->getKontakt()->getOrtID() == $this->ortid;
	}
}

class OrtDistanceMitgliederMatcher extends MitgliederMatcher {
	private $latitude;
	private $longitude;
	private $distance;

	public function __construct($latitude, $longitude, $distance) {
		$this->latitude = $latitude;
		$this->longitude = $longitude;
		$this->distance = $distance;
	}

	public function getLatitude() {
		return $this->latitude;
	}

	public function getLongitude() {
		return $this->longitude;
	}

	public function getDistance() {
		return $this->distance;
	}

	public function match(Mitglied $mitglied) {
		$ort = $mitglied->getLatestRevision()->getKontakt()->getOrt();
		$lat1 = $ort->getLatitude() / 180 * 3.141592;
		$long1 = $ort->getLongitude() / 180 * 3.141592;
		if ($lat1 == null or $long1 == null) {
			return false;
		}
		$lat2 = $this->getLatitude() / 180 * 3.141592;
		$long2 = $this->getLongitude() / 180 * 3.141592;
		// http://en.wikipedia.org/wiki/Haversine_formula
		$dist = 2 * 6371 * acos(pow(($lat1-$lat2)/2,2) + cos($lat1)*cos($lat2)*pow(sin(($lon1-$lon2)/2),2));
		return ($dist <= $this->getDistance());
	}
}

?>
