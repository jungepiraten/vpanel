<?php

require_once(VPANEL_CORE . "/mitgliederfilteraction.class.php");
require_once(VPANEL_PROCESSES . "/mitgliederfilterstatistik.class.php");

class StatistikMitgliederFilterAction extends MitgliederFilterAction {
	public function getLabel() {
		return "Statistik erzeugen";
	}

	public function getPermission() {
		return "mitglieder_delete";
	}

	public function execute($config, $session, $filter, $matcher) {
		$process = new MitgliederFilterStatistikProcess($session->getStorage());
		$process->setTimestamp(time());
		return $this->executeProcess($session, $process, $filter, $matcher);
	}

	public function show($config, $session, $process) {
		return array("statistik" => "showfiles", "tempfiles" => array_map(array($session->getStorage(), "getTempFile"), $process->getTempFileIDs()));
	}
}

?>
