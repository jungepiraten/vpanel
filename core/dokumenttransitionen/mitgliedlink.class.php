<?php

require_once(VPANEL_CORE . "/dokumenttransition.class.php");
require_once(VPANEL_PROCESSES . "/dokumenttransitionmitgliedlink.class.php");

class MitgliedLinkDokumentTransition extends StaticDokumentTransition implements SingleDokumentTransition, MultiDokumentTransition {
	public function __construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $kommentar) {
		parent::__construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $kommentar);
	}

	private function getMitgliedID($session) {
		if ($session->hasVariable("mitgliedid")) {
			return $session->getIntVariable("mitgliedid");
		}
		return null;
	}

	public function execute($config, $session, $filter, $matcher) {
		$process = new DokumentTransaktionMitgliedLinkProcess($session->getStorage());
		$mitgliedid = $this->getMitgliedID($session);
		if ($mitgliedid == null) {
			return array("inputNeeded" => 1);
		}
		$process->setMitgliedID($mitgliedid);
		return $this->executeProcess($session, $process, $filter, $matcher);
	}
}

?>
