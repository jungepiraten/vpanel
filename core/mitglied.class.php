<?php

require_once(VPANEL_CORE . "/globalobject.class.php");

class Mitglied extends GlobalClass {
	private $mitgliedid;
	private $eintrittsdatum;
	private $austritttsdatum;
	private $storage = null;
	private $revisions = array();
	
	public function __construct($storage) {
		$this->storage = $storage;
	}

	public function getRevision($revisionid) {
		if (!isset($this->revisions[$revisionid])) {
			$this->revisions[$revisionid] = $this->storage->getMitgliedRevision();
		}
		return $this->revisions[$revisionid];
	}
}

class MitgliedRevision extends GlobalClass {
	
}

?>
