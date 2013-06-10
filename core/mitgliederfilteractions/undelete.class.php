<?php

require_once(VPANEL_CORE . "/mitgliederfilteraction.class.php");
require_once(VPANEL_PROCESSES . "/mitgliederfilterdelete.class.php");

class UnDeleteMitgliederFilterAction extends MitgliederFilterAction {
	public function execute($config, $session, $filter, $matcher) {
		if (! $session->hasVariable("kommentar")) {
			return array("delete" => "options");
		} else {
			$process = new MitgliederFilterDeleteProcess($session->getStorage());
			$process->setTimestamp(null);
			$process->setKommentar($session->getVariable("kommentar"));

			return $this->executeProcess($session, $process, $filter, $matcher);
		}
	}

	public function show($config, $session, $process) {
		return array("redirect" => $session->getLink("mitglieder"));
	}
}

?>
