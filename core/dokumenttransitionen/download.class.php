<?php

require_once(VPANEL_CORE . "/dokumenttransition.class.php");
require_once(VPANEL_PROCESSES . "/dokumenttransitiondownload.class.php");

class DownloadDokumentTransition extends DokumentTransition implements SingleDokumentTransition, MultiDokumentTransition {
	public function __construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $kommentar) {
		parent::__construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $kommentar);
	}

	private function getProcessPrototype($config, $session) {
		return new DokumentTransaktionDownloadProcess($session->getStorage());
	}

	public function execute($config, $session, $dokumentid) {
		$process = $this->getProcessPrototype($config, $session);
		$process->match($dokumentid);
		return $this->executeProcess($session, $process);
	}

	public function executeMulti($config, $session, $gliederungids, $kategorieid, $statusid) {
		$process = $this->getProcessPrototype($config, $session);
		$process->matchMulti($gliederungids, $kategorieid, $statusid);
		return $this->executeProcess($session, $process);
	}

	public function show($config, $session, $process) {
		return array("redirect" => $session->getLink("tempfile_get", $process->getTempFile()->getTempFileID()));
	}
}

?>
