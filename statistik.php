<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

if (!$session->isAllowed("statistik_show")) {
	$ui->viewLogin();
	exit;
}

$storage = $session->getStorage();

$mitgliedercount = $storage->getMitgliederCount();
$mitgliedercountperms =  $storage->getMitgliederCountPerMs();
$mitgliedercountperstate =  $storage->getMitgliederCountPerState();
$ui->viewStatistik($mitgliedercount, $mitgliedercountperms, $mitgliedercountperstate);

?>
