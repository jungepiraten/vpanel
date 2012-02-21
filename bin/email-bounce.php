<?php

require_once(dirname(__FILE__) . "/../config.inc.php");
$storage = $config->getStorage();

for ($i = 1; $i < $_SERVER["argc"]; $i++) {
	$email = $storage->getEMail($_SERVER["argv"][$i]);
	if ($email != null) {
		$email->setBounceCount($email->getBounceCount() + 1);
		$email->save();
	}
}

?>
