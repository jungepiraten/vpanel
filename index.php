<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

if (!$session->isSignedIn()) {
	$ui->viewLogin();
	exit;
}

require_once(VPANEL_CORE . "/dashboardwidget.class.php");

if ($session->hasVariable("delWidget")) {
	$widgetid = $session->getIntVariable("widgetid");
	$session->getStorage()->getDashboardWidget($widgetid)->delete();
	$ui->redirect($session->getLink("index"));
}
if ($session->hasVariable("widgets")) {
	$widgets = $session->getListVariable("widgets");
	foreach ($widgets as $id => $widget) {
		if (isset($widget["type"])) {
			switch ($widget["type"]) {
			case "static":
				$w = new StaticDashboardWidget($session->getStorage());
				$w->setText($widget["text"]);
				break;
			}
			$w->setColumn($widget["column"]);
			$w->setUser($session->getUser());
			$w->save();
		}
	}
	$ui->redirect($session->getLink("index"));
}

$ui->viewDashboard($session->getUser(), $session->getStorage()->getDashboardWidgetList($session->getUser()->getUserID()));

?>
