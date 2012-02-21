<?php

require_once(dirname(__FILE__) . "/../config.inc.php");
$storage = $config->getStorage();

if ($_SERVER["argc"] <= 1) {
	exit(1);
}

$email = $storage->getEMail($_SERVER["argv"][1]);
if ($email == null) {
	exit(128);
}

$email->setBounceCount($email->getBounceCount() + 1);
$email->save();

?>
