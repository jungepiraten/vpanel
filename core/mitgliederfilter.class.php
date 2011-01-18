<?php

abstract class MitgliederFilter {
	private $filterid;
	private $label;

	public function __construct($filterid, $label) {
		$this->setFilterID($filterid);
		$this->setLabel($label);
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
}

?>
