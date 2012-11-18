<?php

require_once(VPANEL_CORE . "/mitgliederfilteraction.class.php");
require_once(VPANEL_PROCESSES . "/mitgliederfilterstats.class.php");

class StatistikMitgliederFilterAction extends MitgliederFilterAction {
	public function execute($config, $session, $filter, $matcher) {
		$process = new MitgliederFilterStatistikProcess($session->getStorage());
		$process->setTimestamp(time());
		return $this->executeProcess($session, $process, $filter, $matcher);
	}

	public function show($config, $session, $process) {
		return array("stats" => "showfiles", "tempfiles" => array_map(array($session->getStorage(), "getTempFile"), $process->getTempFileIDs()));
	}
}

?>
