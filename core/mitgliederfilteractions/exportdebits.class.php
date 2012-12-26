<?php

require_once(VPANEL_CORE . "/mitgliederfilteraction.class.php");
require_once(VPANEL_PROCESSES . "/mitgliederfilterexportdebits.class.php");

class ExportDebitsMitgliederFilterAction extends MitgliederFilterAction {
	private $streamhandler;
	private $beitragid;

	public function __construct($actionid, $label, $permission, $streamhandler, $beitragid = null) {
		parent::__construct($actionid, $label, $permission);
		$this->streamhandler = $streamhandler;
		$this->beitragid = $beitragid;
	}

	private function getStreamHandler($session) {
		return $this->streamhandler;
	}

	protected function getBeitrag($session) {
		if ($this->beitragid != null) {
			return $session->getStorage()->getBeitrag($this->beitragid);
		}
		if ($session->hasVariable("beitragid")) {
			if ($session->getVariable("beitragid") == "-") {
				return null;
			}
			return $session->getStorage()->getBeitrag($session->getVariable("beitragid"));
		}
		return false;
	}

	public function execute($config, $session, $filter, $matcher) {
		$beitrag = $this->getBeitrag($session);
		if ($beitrag === false) {
			$beitraglist = $session->getStorage()->getBeitragList();
			return array("setbeitrag" => "select", "beitraglist" => $beitraglist);
		}

		$process = new MitgliederFilterExportDebitsProcess($session->getStorage());
		$process->setStreamHandler($this->getStreamHandler($session));
		$process->setBeitrag($beitrag);
		return $this->executeProcess($session, $process, $filter, $matcher);
	}

	public function show($config, $session, $process) {
		if ($process->getStreamHandler() instanceof TempFileStreamHandler) {
			return array("redirect" => $session->getLink("tempfile_get", $process->getStreamHandler()->getTempFile()->getTempFileID()));
		}
	}
}

?>
