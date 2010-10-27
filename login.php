<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
require_once(VPANEL_UI . "/template.class.php");
$ui = new Template($session);

// Login-Seite
if (isset($_POST["login"])) {
	$username = stripslashes($_POST["username"]);
	$password = stripslashes($_POST["password"]);

	try {
		$session->login($username, $password);
	} catch (Exception $e) {
		
	}
}
$ui->viewLogin();

?>
