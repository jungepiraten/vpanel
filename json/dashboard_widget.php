<?php

require_once(dirname(__FILE__) . "/../config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

$widget = $session->getStorage()->getDashboardWidget($session->getVariable("widgetid"));
if ($widget->getUserID() == $session->getUser()->getUserID()) {
	$ui->viewDashboardWidget($widget);
}

?>
