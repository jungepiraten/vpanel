<?php

require_once(VPANEL_CORE . "/dokumenttransition.class.php");
require_once(VPANEL_PROCESSES . "/dokumenttransitionprint.class.php");

class PrintDokumentTransition extends StaticDokumentTransition implements SingleDokumentTransition, MultiDokumentTransition {
	private $options;

	public function __construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $nextflagids, $kommentar, $options) {
		parent::__construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $nextflagids, $kommentar);
		$this->options = $options;
	}

	public function execute($config, $session, $filter, $matcher) {
		$process = new DokumentTransaktionPrintProcess($session->getStorage());
		$process->setOptions($options);
		return $this->executeProcess($session, $process, $filter, $matcher);
	}
}

?>
