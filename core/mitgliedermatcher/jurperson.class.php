<?php

require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

class JurPersonMitgliederMatcher extends MitgliederMatcher {
	public function match(Mitglied $mitglied) {
		return $mitglied->getLatestRevision()->isJurPerson();
	}
}

?>
