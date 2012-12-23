<?php

require_once(VPANEL_CORE . "/dokumenttransition.class.php");

class MitgliedCreateDokumentTransition extends StaticDokumentTransition implements SingleDokumentTransition {
	private $mitgliedtemplateid;

	public function __construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $kommentar, $mitgliedtemplateid) {
		parent::__construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $kommentar);
		$this->mitgliedtemplateid = $mitgliedtemplateid;
	}

	public function execute($config, $session, $filter, $matcher) {
		// TODO hacky and needs to be rewritten
		return array("redirect" => $session->getLink("dokumente_mitglied_create", $matcher->getDokumentID(), $this->mitgliedtemplateid));
	}

	public function show($config, $session, $process) {
	}
}

class DynamicMitgliedCreateDokumentTransition extends StaticDokumentTransition implements SingleDokumentTransition {
	public function __construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $kommentar, $mitgliedtemplatechooser) {
		parent::__construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $kommentar);
		$this->mitgliedtemplatechooser = $mitgliedtemplatechooser;
	}

	public function execute($config, $session, $filter, $matcher) {
		// TODO hacky and needs to be rewritten
		$dokument = $session->getStorage()->getDokument($matcher->getDokumentID());
		$mitgliedtemplateid = $this->mitgliedtemplatechooser->getMitgliedTemplateID($session->getStorage(), $dokument);
		if (is_array($mitgliedtemplateid)) {
			if (count($mitgliedtemplateid) == 1) {
				$mitgliedtemplateid = array_shift($mitgliedtemplateid);
			} else {
				if ($session->hasVariable("mitgliedtemplateid") && in_array($session->getVariable("mitgliedtemplateid"), $mitgliedtemplateid)) {
					$mitgliedtemplateid = $session->getVariable("mitgliedtemplateid");
				} else {
					return array("selectMitgliedTemplate" => 1, "mitgliedtemplates" => array_map(array($session->getStorage(), "getMitgliederTemplate"), $mitgliedtemplateid));
				}
			}
		}
		return array("redirect" => $session->getLink("dokumente_mitglied_create", $matcher->getDokumentID(), $mitgliedtemplateid));
	}

	public function show($config, $session, $process) {
	}
}

interface MitgliedTemplateChooserDokument {
	public function getMitgliedTemplateID($storage, $dokument);
}

?>
