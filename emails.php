<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

if (!$session->isSignedIn()) {
	$ui->viewLogin();
	exit;
}

switch ($session->hasVariable("mode") ? $session->getVariable("mode") : null) {
case "delbounce":
	$bounce = $session->getStorage()->getEMailBounce($session->getVariable("bounceid"));
	$bounce->delete();
	$ui->redirect();

	break;
case "listbounces":
	if ($session->hasVariable("mitgliederrevisionid")) {
		if (!$session->isAllowed("mitglieder_show")) {
			$ui->viewLogin();
			exit;
		}

		$email = $session->getStorage()->getMitgliederRevision($session->getVariable("mitgliederrevisionid"))->getKontakt()->getEMail();
	} else {
		exit;
	}
	$bounces = $email->getBounces();

	$ui->viewEMailBounceList($bounces);
	break;
}

?>
