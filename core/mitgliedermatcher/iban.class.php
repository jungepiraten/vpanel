<?php

require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

class IBANMitgliederMatcher extends MitgliederMatcher {
	public function match(Mitglied $mitglied) {
		return $mitglied->getKontakt()->getIBan() != null;
	}
}

?>
