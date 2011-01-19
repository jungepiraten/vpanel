<?php

require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

class NatPersonMitgliederMatcher extends MitgliederMatcher {
	public function match(Mitglied $mitglied) {
		return $mitglied->getLatestRevision()->isNatPerson();
	}
}

?>
