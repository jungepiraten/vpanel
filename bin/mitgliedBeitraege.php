<?php

require_once(dirname(__FILE__) . "/../config.inc.php");
$storage = $config->getStorage();

$mitglieder = $storage->getMitgliederResult();
while ($mitglied = $mitglieder->fetchRow()) {
	$beitragtimeformat = $mitglied->getLatestRevision()->getBeitragTimeFormat();
	$beitrag = $beitragtimeformat->getBeitrag();
	$mitgliedbeitrag = $mitglied->getBeitrag($beitrag->getBeitragID());
	if ($mitgliedbeitrag->getHoehe() == null) {
		$mitgliedbeitrag->setHoehe($mitglied->getLatestRevision()->getBeitrag());
		$mitglied->save();
	}
}

?>
