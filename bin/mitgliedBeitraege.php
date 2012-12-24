<?php

require_once(dirname(__FILE__) . "/../config.inc.php");
$storage = $config->getStorage();

require_once(VPANEL_MITGLIEDERMATCHER . "/logic.class.php");
require_once(VPANEL_MITGLIEDERMATCHER . "/ausgetreten.class.php");

$mitglieder = $storage->getMitgliederResult(new NotMitgliederMatcher(new AusgetretenMitgliederMatcher()));
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
