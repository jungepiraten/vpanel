<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

// Login-Seite
if ($session->getBoolVariable("login")) {
	$username = $session->getVariable("username");
	$password = $session->getVariable("password");

	try {
		$session->login($username, $password);
		$ui->redirect();
	} catch (Exception $e) {
		$ui->viewLogin(true);
		exit;
	}
}

if ($session->getBoolVariable("logout")) {
	$session->logout();
	$ui->redirect();
}

if ($session->isSignedIn()) {
	$ui->redirect($session->getLink("index"));
} else {
	$ui->viewLogin();
}

?>
