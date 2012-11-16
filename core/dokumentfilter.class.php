<?php

require_once(VPANEL_CORE . "/aktion.class.php");
require_once(VPANEL_DOKUMENTMATCHER . "/logic.class.php");
require_once(VPANEL_DOKUMENTMATCHER . "/gliederung.class.php");

class DokumentFilter extends Aktion {
	private $filterid;
	private $matcher;

	public function __construct($filterid, $label, $permission, $gliederungid, $matcher) {
		parent::__construct($label, $permission, $gliederungid);
		$this->filterid = $filterid;
		if ($gliederungid != null) {
			$matcher = new AndDokumentMatcher(new GliederungDokumentMatcher($gliederungid), $matcher);
		}
		$this->matcher = $matcher;
	}

	public function getFilterID() {
		return $this->filterid;
	}

	public function getMatcher() {
		return $this->matcher;
	}

	public function match($dokument) {
		return $this->matcher->match($dokument);
	}
}

abstract class DokumentMatcher {
	abstract public function match(Dokument $dokument);
}

?>
