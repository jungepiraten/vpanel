<?php

require_once(VPANEL_CORE . "/mitgliederfilter.class.php");

class TrueMitgliederMatcher extends MitgliederMatcher {
	public function match(Mitglied $mitglied) {
		return true;
	}
}

class FalseMitgliederMatcher extends MitgliederMatcher {
	public function match(Mitglied $mitglied) {
		return false;
	}
}

abstract class LinkedLogicMitgliederMatcher extends MitgliederMatcher {
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

class AndMitgliederMatcher extends LinkedLogicMitgliederMatcher {
	public function match(Mitglied $mitglied) {
		$m = true;
		foreach ($this->getConditions() as $filter) {
			$m = $m && $filter->match($mitglied);
		}
		return $m;
	}
}

class OrMitgliederMatcher extends LinkedLogicMitgliederMatcher {
	public function match(Mitglied $mitglied) {
		$m = false;
		foreach ($this->getConditions() as $filter) {
			$m = $m || $filter->match($mitglied);
		}
		return $m;
	}
}

abstract class SingleLogicMitgliederMatcher extends MitgliederMatcher {
	protected $filter;

	public function __construct($filter) {
		$this->filter = $filter;
	}

	public function getCondition() {
		return $this->filter;
	}
}

class NotMitgliederMatcher extends SingleLogicMitgliederMatcher {
	public function match(Mitglied $mitglied) {
		return !$this->getCondition()->match($mitglied);
	}
}

?>
