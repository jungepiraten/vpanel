<?php

require_once(dirname(__FILE__) . "/../config.inc.php");
$storage = $config->getStorage();

$ortResult = $storage->getOrtResult();
while ($ort = $ortResult->fetchRow()) {
	if ($ort->getLatitude() == null || $ort->getLongitude() == null) {
		$plz = $ort->getPLZ();
		$ortname = str_replace(array("ä","ö","ü","ß"), array("ae", "oe", "ue", "ss"), $ort->getLabel());
		$state = str_replace(array("ä","ö","ü","ß"), array("ae", "oe", "ue", "ss"), $ort->getState()->getLabel());
		$country = str_replace(array("ä","ö","ü","ß"), array("ae", "oe", "ue", "ss"), $ort->getState()->getCountry()->getLabel());
		$data = json_decode(file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($plz." ".$ortname." ".$state." ".$country) . "&sensor=false"));

		if (count($data->results) > 0) {
			$location = $data->results[0]->geometry->location;
			$ort->setLatitude($location->lat);
			$ort->setLongitude($location->lng);
			$ort->save();
		}
	}
}

?>
