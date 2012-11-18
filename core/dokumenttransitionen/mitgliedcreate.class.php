<?php

require_once(VPANEL_CORE . "/dokumenttransition.class.php");

class MitgliedCreateDokumentTransition extends StaticDokumentTransition implements SingleDokumentTransition {
	private $mitgliedtemplateid;

	public function __construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $kommentar, $mitgliedtemplateid) {
		parent::__construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $kommentar);
		$this->mitgliedtemplateid = $mitgliedtemplateid;
	}

	public function execute($config, $session, $dokumentid) {
		return array("redirect" => $session->getLink("dokumente_mitglied_create", $dokumentid, $this->mitgliedtemplateid));
	}

	public function show($config, $session, $process) {
	}
}

class DynamicMitgliedCreateDokumentTransition extends StaticDokumentTransition implements SingleDokumentTransition {
	public function __construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $kommentar, $mitgliedtemplatechooser) {
		parent::__construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $kommentar);
		$this->mitgliedtemplatechooser = $mitgliedtemplatechooser;
	}

	public function execute($config, $session, $dokumentid) {
		$dokument = $session->getStorage()->getDokument($dokumentid);
		return array("redirect" => $session->getLink("dokumente_mitglied_create", $dokumentid, $this->mitgliedtemplatechooser->getMitgliedTemplateID($session->getStorage(), $dokument)));
	}

	public function show($config, $session, $process) {
	}
}

interface MitgliedTemplateChooserDokument {
	public function getMitgliedTemplateID($storage, $dokument);
}

?>
