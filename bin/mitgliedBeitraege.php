<?php

require_once(dirname(__FILE__) . "/../config.inc.php");
$storage = $config->getStorage();

require_once(VPANEL_MITGLIEDERMATCHER . "/logic.class.php");
require_once(VPANEL_MITGLIEDERMATCHER . "/ausgetreten.class.php");

$beitraege = array();
foreach ($storage->getBeitragTimeFormatList() as $beitragtimeformat) {
	$beitraege[$beitragtimeformat->getBeitragTimeFormatID()] = $beitragtimeformat->getBeitrag()->getBeitragID();
}

$mitglieder = $storage->getMitgliederResult(new NotMitgliederMatcher(new AusgetretenMitgliederMatcher()));
while ($mitglied = $mitglieder->fetchRow()) {
	$hasBeitrag = false;
	foreach ($beitraege as $beitragid) {
		if ($mitglied->hasBeitrag($beitragid)) {
			$hasBeitrag = true;
		}
	}

	if (! $hasBeitrag) {
		$beitragid = $beitraege[$mitglied->getLatestRevision()->getBeitragTimeFormatID()];
		$mitgliedbeitrag = $mitglied->getBeitrag($beitragid);
		$mitgliedbeitrag->setHoehe($mitglied->getLatestRevision()->getBeitrag());
		$mitglied->save();
	}
}

?>
