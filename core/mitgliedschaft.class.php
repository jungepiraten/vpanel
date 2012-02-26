<?php

require_once(VPANEL_CORE . "/storageobject.class.php");
require_once(VPANEL_CORE . "/mailtemplate.class.php");
require_once(VPANEL_CORE . "/mitgliedermatcher/logic.class.php");
require_once(VPANEL_CORE . "/mitgliedermatcher/mitgliedschaft.class.php");

class Mitgliedschaft extends StorageClass {
	private $mitgliedschaftid;
	private $label;
	private $description;

	private $defaultcreatemail;

	public static function factory(Storage $storage, $row) {
		$mitgliedschaft = new Mitgliedschaft($storage);
		$mitgliedschaft->setMitgliedschaftID($row["mitgliedschaftid"]);
		$mitgliedschaft->setLabel($row["label"]);
		$mitgliedschaft->setDescription($row["description"]);
		return $mitgliedschaft;
	}

	public function getMitgliedschaftID() {
		return $this->mitgliedschaftid;
	}

	public function setMitgliedschaftID($mitgliedschaftid) {
		$this->mitgliedschaftid = $mitgliedschaftid;
	}

	public function getLabel() {
		return $this->label;
	}

	public function setLabel($label) {
		$this->label = $label;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setDescription($description) {
		$this->description = $description;
	}

	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$this->setMitgliedschaftID( $storage->setMitgliedschaft(
			$this->getMitgliedschaftID(),
			$this->getLabel(),
			$this->getDescription() ));
	}

	public function getMitgliederCount() {
		return $this->getStorage()->getMitgliederCount(new AndMitgliederMatcher(	new MitgliedschaftMitgliederMatcher($this->getMitgliedschaftID()),
												new NotMitgliederMatcher(new AusgetretenMitgliederMatcher()) ));
	}
}

?>
