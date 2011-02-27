<?php

require_once(dirname(__FILE__) . "/../config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();

$process = $session->getStorage()->getProcess($session->getVariable("processid"));

$row = array();
$row["progress"] = $process->getProgress();
$row["iswaiting"] = $process->isWaiting();
$row["isrunning"] = $process->isRunning();
$row["isfinished"] = $process->isFinished();
$row["queued"] = $process->getQueued();
$row["started"] = $process->getStarted();
$row["finished"] = $process->getFinished();
$row["finishedpage"] = $process->getFinishedPage();

if ($process->isRunning() && $process->getProgress() > 0) {
	// Jaja, sehr ungenau
	$row["estfinished"] = round($process->getStarted() + (time() - $process->getStarted()) / $process->getProgress());
	$row["eta"] = $row["estfinished"] - time();
}

print(json_encode($row));

?>
