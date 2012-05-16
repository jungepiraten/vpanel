<?php

require_once(VPANEL_CORE . "/dokumenttransition.class.php");

class MitgliedCreateDokumentTransition extends DokumentTransition implements SingleDokumentTransition {
	private $mitgliedtemplateid;
	private $label;

	public function __construct($transitionid, $hidden, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $kommentar, $mitgliedtemplateid, $label) {
		parent::__construct($transitionid, $hidden, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $kommentar);
		$this->mitgliedtemplateid = $mitgliedtemplateid;
		$this->label = $label;
	}

	public function getLabel() {
		return $this->label;
	}

	public function getPermission() {
		return "mitglieder_create";
	}

	public function execute($config, $session, $dokumentid) {
		return array("redirect" => $session->getLink("dokumente_mitglied_create", $dokumentid, $this->mitgliedtemplateid));
	}

	public function show($config, $session, $process) {
	}
}

?>
