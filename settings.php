<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

if (!$session->isSignedIn()) {
	$ui->viewLogin();
	exit;
}

$success = false;
$invalidpassword = false;
$pwsnotequal = false;
$pwtooshort = false;

if ($session->getBoolVariable("changepassword")) {
	$pw_alt = $session->getVariable("pw_alt");
	$pw_neu = $session->getVariable("pw_neu");
	$pw_neu2 = $session->getVariable("pw_neu2");

	if (!$session->getUser()->isValidPassword($pw_alt)) {
		$invalidpassword = true;
	} else if ($pw_neu != $pw_neu2) {
		$pwsnotequal = true;
	} else if (strlen(trim($pw_neu)) < 6) {
		$pwtooshort = true;
	} else {
		$session->getUser()->changePassword($pw_neu);
		$session->getUser()->save();
		$success = true;
	}
}

$ui->viewEinstellungen($success, $invalidpassword, $pwsnotequal, $pwtooshort);

?>
