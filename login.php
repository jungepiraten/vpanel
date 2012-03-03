<?php

require_once(dirname(__FILE__) . "/config.inc.php");

$session = $config->getSession();
$ui = $session->getTemplate();

// Login-Seite
if ($session->getBoolVariable("login")) {
	$username = $session->getVariable("username");
	$password = $session->getVariable("password");

	$user = $session->getStorage()->getUserByUsername($username);
	if ($user == null || !$user->isValidPassword($password)) {
		$ui->viewLogin(true);
		exit;
	}
	
	$session->setUser($user);
	$ui->redirect();
}

if ($session->getBoolVariable("logout")) {
	$session->setUser(null);
	$ui->redirect();
}

if ($session->isSignedIn()) {
	$ui->redirect($session->getLink("index"));
} else {
	$ui->viewLogin();
}

?>
