<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

switch ($session->hasVariable("mode") ? $session->getVariable("mode") : null) {
case "listbounces":
	if ($session->hasVariable("emailid")) {
		$email = $session->getStorage()->getEMail($session->getVariable("emailid"));
		$bounces = $email->getBounces();
	} else {
		exit;
	}

	$ui->viewEMailBounceList($bounces);
	break;
}

?>
