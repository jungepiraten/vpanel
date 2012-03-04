<?php

require_once(dirname(__FILE__) . "/../config.inc.php");
$storage = $config->getStorage();

$storage->delSessionDataBefore(time() - 24*60*60);

$processes = $storage->getProcessList();
foreach ($processes as $process) {
	if ($process->isFinished() && $process->getFinished() < time() - 24*60*60) {
		$process->delete();
	}
}

$tempfiles = $storage->getTempFileList();
foreach ($tempfiles as $tempfile) {
	if ($tempfile->getTimestamp() < time() - 24*60*60) {
		$tempfile->delete();
	}
}

$statistiken = $storage->getMitgliederStatistikList();
foreach ($statistiken as $statistik) {
	if ($statistik->getTimestamp() < time() - 24*60*60) {
		$statistik->delete();
	}
}

?>
