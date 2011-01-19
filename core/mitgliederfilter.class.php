<?php

class MitgliederFilter {
	private $filterid;
	private $label;
	private $matcher;

	public function __construct($filterid, $label, $matcher) {
		$this->setFilterID($filterid);
		$this->setLabel($label);
		$this->setMatcher($matcher);
	}
	
	public function getFilterID() {
		return $this->filterid;
	}
	
	public function setFilterID($filterid) {
		$this->filterid = $filterid;
	}
	
	public function getLabel() {
		return $this->label;
	}
	
	public function setLabel($label) {
		$this->label = $label;
	}
	
	public function getMatcher() {
		return $this->matcher;
	}
	
	public function setMatcher($matcher) {
		$this->matcher = $matcher;
	}

	public function match($mitglied) {
		return $this->matcher->match($mitglied);
	}
}

abstract class MitgliederMatcher {
	abstract public function match(Mitglied $mitglied);
}

?>
