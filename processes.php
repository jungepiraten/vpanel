<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

switch ($session->hasVariable("mode") ? $session->getVariable("mode") : null) {
case "view":
	$process = $session->getStorage()->getProcess($session->getVariable("processid"));
	$ui->viewProcess($process);
	exit;
}

?>
