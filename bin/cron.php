<?php

require_once(dirname(__FILE__) . "/config.inc.php");
$storage = $config->getStorage();

$processes = $storage->getProcessList();
foreach ($processes as $process) {
	if ($process->isWaiting()) {
		$process->run();
	}
}

?>
