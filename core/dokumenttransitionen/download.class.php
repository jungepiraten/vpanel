<?php

require_once(VPANEL_CORE . "/dokumenttransition.class.php");
require_once(VPANEL_PROCESSES . "/dokumenttransitiondownload.class.php");

class DownloadDokumentTransition extends StaticDokumentTransition implements SingleDokumentTransition, MultiDokumentTransition {
	public function __construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $kommentar) {
		parent::__construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $kommentar);
	}

	public function execute($config, $session, $filter, $matcher) {
		$process = new DokumentTransaktionDownloadProcess($session->getStorage());
		return $this->executeProcess($session, $process, $filter, $matcher);
	}

	public function show($config, $session, $process) {
		return array("redirect" => $session->getLink("tempfile_get", $process->getTempFile()->getTempFileID()));
	}
}

?>
