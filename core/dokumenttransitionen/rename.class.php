<?php

require_once(VPANEL_CORE . "/dokumenttransition.class.php");
require_once(VPANEL_PROCESSES . "/dokumenttransitionrename.class.php");

class RenameDokumentTransition extends DokumentTransition implements SingleDokumentTransition {
	private $dokumenttemplate;

	public function __construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid, $dokumenttemplate) {
		parent::__construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid);
		$this->dokumenttemplate = $dokumenttemplate;
	}

	public function getDokumentTemplate() {
		return $this->dokumenttemplate;
	}

	public function getNextStatusID($session) {
		return $this->getDokumentTemplate()->getDokumentStatusID($session);
	}

	public function getNextKategorieID($session) {
		return $this->getDokumentTemplate()->getDokumentKategorieID($session);
	}

	public function getNotizKommentar($session) {
		return $this->getDokumentTemplate()->getDokumentKommentar($session);
	}

	public function execute($config, $session, $dokumentid) {
		if ($session->hasVariable("save")) {
			$process = new DokumentTransaktionRenameProcess($session->getStorage());
			$process->setNextIdentifier($this->getDokumentTemplate()->getDokumentIdentifier($session));
			$process->setNextLabel($this->getDokumentTemplate()->getDokumentLabel($session));
			$process->match($dokumentid);
			return $this->executeProcess($session, $process);
		}
		return array();
	}
}

?>
