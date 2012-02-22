<?php

require_once(dirname(__FILE__) . "/../config.inc.php");
$storage = $config->getStorage();

require_once(VPANEL_CORE . "/emailbounce.class.php");

if ($_SERVER["argc"] <= 1) {
	echo "Usage: " . $_SERVER["argv"][0] . " <id>" . "\n";
	exit(1);
}

$email = $storage->getEMail($_SERVER["argv"][1]);
if ($email == null) {
	echo "email not found" . "\n";
	exit(128);
}

$bounce = new EMailBounce($storage);
$bounce->setEMail($email);
$bounce->setTimestamp(time());
$bounce->setMessage(file_get_contents("php://stdin"));
$bounce->save();

?>
