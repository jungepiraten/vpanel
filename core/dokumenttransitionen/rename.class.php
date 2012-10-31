<?php

require_once(VPANEL_CORE . "/dokumenttransition.class.php");
require_once(VPANEL_PROCESSES . "/dokumenttransitionrename.class.php");

class RenameDokumentTransition extends DokumentTransition implements SingleDokumentTransition {
	private $dokumenttemplate;

	public function __construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid, $dokumenttemplate = null) {
		parent::__construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid);
		$this->dokumenttemplate = $dokumenttemplate;
	}

	public function getDokumentTemplate($session) {
		if ($this->dokumenttemplate != null) {
			return $this->dokumenttemplate;
		}
		if ($session->hasVariable("templateid") && $session->getStorage()->hasDokumentTemplate($session->getVariable("templateid"))) {
			return $session->getStorage()->getDokumentTemplate($session->getVariable("templateid"));
		}
		return null;
	}

	public function getNextStatusID($session) {
		return $this->getDokumentTemplate($session)->getDokumentStatusID($session);
	}

	public function getNextKategorieID($session) {
		return $this->getDokumentTemplate($session)->getDokumentKategorieID($session);
	}

	public function getNotizKommentar($session) {
		return $this->getDokumentTemplate($session)->getDokumentKommentar($session);
	}

	public function execute($config, $session, $dokumentid) {
		if ($this->getDokumentTemplate($session) == null) {
			return array("selectTransition" => 1, "templates" => $config->getStorage()->getDokumentTemplateList($session));
		}

		if ($session->hasVariable("save")) {
			$process = new DokumentTransaktionRenameProcess($session->getStorage());
			$process->setNextIdentifier($this->getDokumentTemplate($session)->getDokumentIdentifier($session));
			$process->setNextLabel($this->getDokumentTemplate($session)->getDokumentLabel($session));
			$process->match($dokumentid);
			return $this->executeProcess($session, $process);
		}
		return array("template" => $this->getDokumentTemplate($session));
	}
}

?>
