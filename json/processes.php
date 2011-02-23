<?php

require_once(dirname(__FILE__) . "/../config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();

$process = $session->getStorage()->getProcess($session->getVariable("processid"));

$row = array();
$row["progress"] = $process->getProgress();

print(json_encode($row));

?>
