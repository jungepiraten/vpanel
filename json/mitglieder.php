<?php

require_once(dirname(__FILE__) . "/../config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();

if (!$session->isAllowed("mitglieder_show")) {
	exit;
}

require_once(VPANEL_MITGLIEDERMATCHER . "/search.class.php");

$matcher = new SearchMitgliederMatcher(explode(' ', $session->getVariable("q")));

$mitglieder = $session->getStorage()->getMitgliederList($matcher, 5);
$jsons = array();

foreach ($mitglieder as $mitglied) {
	$row = array();
	$row["label"] = rand(100,666);
	$jsons[] = $row;
}

print(json_encode($jsons));

?>
