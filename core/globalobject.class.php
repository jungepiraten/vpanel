<?php

abstract class GlobalClass {
	private $globalid;
	
	public function __construct($globalid) {
		$this->setGlobalId($globalid);
	}
	
	public function getGlobalId() {
		return $this->globalid;
	}

	public function setGlobalId($globalid) {
		$this->globalid = $globalid;
	}
}

?>
