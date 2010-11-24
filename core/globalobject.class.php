<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

abstract class GlobalClass extends StorageClass {
	private $globalid = null;
	
	public function __construct(Storage $storage = null, $globalid = null) {
		parent::__construct($storage);
		$this->setGlobalID($globalid);
	}
	
	public function getGlobalID() {
		return $this->globalid;
	}

	public function setGlobalID($globalid) {
		$this->globalid = $globalid;
	}
}

?>
