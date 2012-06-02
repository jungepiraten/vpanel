<?php

require_once(VPANEL_CORE . "/dokumenttransition.class.php");

class MitgliedLinkDokumentTransition extends DokumentTransition implements SingleDokumentTransition {
	public function __construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $kommentar) {
		parent::__construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $kommentar);
	}

	public function execute($config, $session, $dokumentid) {
		return array("redirect" => $session->getLink("dokumente_mitglied", $dokumentid));
	}

	public function show($config, $session, $process) {
	}
}

?>
