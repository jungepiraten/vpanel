<?php

require_once(VPANEL_CORE . "/dokumentfilter.class.php");

class TrueDokumentMatcher extends DokumentMatcher {
	public function match(Dokument $dokument) {
		return true;
	}
}

class FalseDokumentMatcher extends DokumentMatcher {
	public function match(Dokument $dokument) {
		return false;
	}
}

abstract class LinkedLogicDokumentMatcher extends DokumentMatcher {
	protected $filters;

	public function __construct($filters) {
		if (is_array($filters)) {
			$this->filters = $filters;
		} else {
			$this->filters = func_get_args();
		}
	}

	public function getConditions() {
		return $this->filters;
	}
}

class AndDokumentMatcher extends LinkedLogicDokumentMatcher {
	public function match(Dokument $dokument) {
		$m = true;
		foreach ($this->getConditions() as $filter) {
			$m = $m && $filter->match($mitglied);
		}
		return $m;
	}
}

class OrDokumentMatcher extends LinkedLogicDokumentMatcher {
	public function match(Dokument $dokument) {
		$m = false;
		foreach ($this->getConditions() as $filter) {
			$m = $m || $filter->match($mitglied);
		}
		return $m;
	}
}

abstract class SingleLogicDokumentMatcher extends DokumentMatcher {
	protected $filter;

	public function __construct($filter) {
		$this->filter = $filter;
	}

	public function getCondition() {
		return $this->filter;
	}
}

class NotDokumentMatcher extends SingleLogicDokumentMatcher {
	public function match(Dokument $dokument) {
		return !$this->getCondition()->match($mitglied);
	}
}

?>
