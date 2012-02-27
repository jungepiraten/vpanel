<?php

require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

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
		$lat1 = $ort->getLatitude();
		$long1 = $ort->getLongitude();
		if ($lat1 == null or $long1 == null) {
			return false;
		}
		$lat2 = $this->getLatitude();
		$long2 = $this->getLongitude();
		// http://en.wikipedia.org/wiki/Haversine_formula
		$dist = 2*6371 * asin(sqrt(pow(sin(($lat1 - $lat2) / 2),2) + cos($lat1) * cos($lat2) * pow(sin(($long1 - $long2) / 2),2)));
		return ($dist <= $this->getDistance());
	}
}

?>
