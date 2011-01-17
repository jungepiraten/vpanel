<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class MitgliederFilter extends StorageClass {
	private $filterid;
	private $label;
	
	public static function factory(Storage $storage, $row) {
		$filter = new MitgliederFilter($storage);
		$filter->setFilterID($row["filterid"]);
		$filter->setLabel($row["label"]);
		return $filter;
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

	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$this->setFilterID( $storage->setMitgliederFilter(
			$this->getFilterID(),
			$this->getLabel() ));
	}
}

?>
