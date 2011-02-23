<?php

require_once(dirname(__FILE__) . "/config.inc.php");
$session = $config->getSession();

$processes = $session->getStorage()->getProcessList();
foreach ($processes as $process) {
	if ($process->isWaiting()) {
		$process->run();
	}
}

?>
