<?php

require_once(VPANEL_CORE . "/mitgliederfilteraction.class.php");
require_once(VPANEL_PROCESSES . "/mitgliederfilterdelete.class.php");

class DeleteMitgliederFilterAction extends MitgliederFilterAction {
	public function execute($config, $session, $filter, $matcher) {
		$process = new MitgliederFilterDeleteProcess($session->getStorage());
		$process->setTimestamp(time());
		return $this->executeProcess($session, $process, $filter, $matcher);
	}

	public function show($config, $session, $process) {
		return array("redirect" => $session->getLink("mitglieder"));
	}
}

?>
