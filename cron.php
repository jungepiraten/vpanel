<?php

require_once(dirname(__FILE__) . "/config.inc.php");

$processes = $config->getStorage()->getProcessList();
foreach ($processes as $process) {
	if ($process->isWaiting()) {
		$process->run();
	}
}

?>
