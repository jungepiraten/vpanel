<?php

require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

class RevisionFlagMitgliederMatcher extends MitgliederMatcher {
	private $flagid;

	public function __construct($flagid) {
		$this->flagid = $flagid;
	}

	public function getFlagID() {
		return $this->flagid;
	}

	public function match(Mitglied $mitglied) {
		return $mitglied->getLatestRevision()->hasFlag($this->flagid);
	}
}

?>
