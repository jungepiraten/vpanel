<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

abstract class GlobalClass extends StorageClass {
	private $globalid = null;
	
	public function __construct(Storage $storage = null, $globalid = null) {
		parent::__construct($storage);
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
