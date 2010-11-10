<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

// Login-Seite
if (isset($_POST["login"])) {
	$username = stripslashes($_POST["username"]);
	$password = stripslashes($_POST["password"]);

	try {
		$session->login($username, $password);
		$ui->redirect();
	} catch (Exception $e) {
		$ui->viewLogin(true);
		exit;
	}
}

if (isset($_REQUEST["logout"])) {
	$session->logout();
	$ui->redirect();
}

if ($session->getAuth()->isSignedIn()) {
	$ui->redirect($session->getLink("index"));
} else {
	$ui->viewLogin();
}

?>
