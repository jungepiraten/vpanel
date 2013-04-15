<?php

require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

class KontoMitgliederMatcher extends MitgliederMatcher {
	public function match(Mitglied $mitglied) {
		return $mitglied->getLatestRevision()->getKontakt()->getKontoID() != null;
	}
}

?>
