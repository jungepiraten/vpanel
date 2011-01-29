<?php

require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

class AusgetretenMitgliederMatcher extends MitgliederMatcher {
	public function match(Mitglied $mitglied) {
		return $mitglied->isAusgetreten();
	}
}

?>
