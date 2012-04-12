<?php

require_once(dirname(__FILE__) . "/../config.inc.php");
$storage = $config->getStorage();

while (true) {
	$processes = $storage->getProcessList();
	foreach ($processes as $process) {
		if ($process->isWaiting()) {
			$process->run();
		}
	}
	sleep(30);
}

?>
