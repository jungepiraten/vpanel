<?php

require_once(VPANEL_CORE . "/mitgliederfilteraction.class.php");
require_once(VPANEL_PROCESSES . "/mitgliederfilterdelete.class.php");

class DeleteMitgliederFilterAction extends MitgliederFilterAction {
	public function execute($config, $session, $filter, $matcher) {
		if (! $session->hasVariable("timestamp")) {
			return array("delete" => "options");
		} else {
			$timestamp = $session->getTimestampVariable("timestamp");
			$process = new MitgliederFilterDeleteProcess($session->getStorage());
			$process->setTimestamp($timestamp);
			return $this->executeProcess($session, $process, $filter, $matcher);
		}
	}

	public function show($config, $session, $process) {
		return array("redirect" => $session->getLink("mitglieder"));
	}
}

?>
