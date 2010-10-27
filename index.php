<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
require_once(VPANEL_UI . "/template.class.php");
$ui = new Template($session);
// Startseite

$ui->viewLogin();

?>
