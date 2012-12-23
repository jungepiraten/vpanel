<?php

require_once(VPANEL_CORE . "/dokumenttransition.class.php");
require_once(VPANEL_PROCESSES . "/dokumenttransitionforward.class.php");

class ForwardDokumentTransition extends StaticDokumentTransition implements SingleDokumentTransition, MultiDokumentTransition {
	private $email;

	public function __construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $kommentar, $email = null) {
		parent::__construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $kommentar);
		$this->email = $email;
	}

	private function getForwardDestination($session) {
		if ($this->email != null) {
			return $session->getStorage()->searchEMail($this->email);
		}
		if ($session->hasVariable("email")) {
			return $session->getStorage()->searchEMail($session->getVariable("email"));
		}
		return null;
	}

	public function execute($config, $session, $filter, $matcher) {
		$process = new DokumentTransaktionForwardProcess($session->getStorage());
		$destination = $this->getForwardDestination($session);
		if ($destination == null) {
			return array("inputNeeded" => 1);
		}
		$process->setForwardEMail($destination);
		return $this->executeProcess($session, $process, $filter, $matcher);
	}
}

?>
