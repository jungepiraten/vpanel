<?php

require_once(VPANEL_CORE . "/dokumenttransition.class.php");

class MitgliedLinkDokumentTransition extends StaticDokumentTransition implements SingleDokumentTransition {
	public function __construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $kommentar) {
		parent::__construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $kommentar);
	}

	public function execute($config, $session, $filter, $matcher) {
		// TODO hacky and needs to be rewritten
		return array("redirect" => $session->getLink("dokumente_mitglied", $matcher->getDokumentID()));
	}
}

?>
