<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

switch ($session->hasVariable("mode") ? $session->getVariable("mode") : null) {
case "delbounce":
	$session->getStorage()->delEMailBounce($session->getVariable("bounceid"));
	$ui->redirect();

	break;
case "listbounces":
	if ($session->hasVariable("mitgliederrevisionid")) {
		$email = $session->getStorage()->getMitgliederRevision($session->getVariable("mitgliederrevisionid"))->getKontakt()->getEMail();
	} else {
		exit;
	}
	$bounces = $email->getBounces();

	$ui->viewEMailBounceList($bounces);
	break;
}

?>
