<?php

require_once(dirname(__FILE__) . "/../config.inc.php");

$session = $config->getSession(true);
$api = $session->getTemplate();

$user = null;
if ($session->hasVariable("username")) {
	$user = $session->getStorage()->getUserByUserName($session->getVariable("username"));
}

$session->initSession($user);
$api->output();

?>
