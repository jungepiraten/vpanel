<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class MitgliedRevisionTextField extends StorageClass {
	private $textfieldid;
	private $revisionid;
	private $value;

	private $textfield;
	private $revision;

	public static function factory(Storage $storage, $row) {
		$revisiontextfield = new MitgliedRevisionTextField($storage);
		$revisiontextfield->setTextFieldID($row["textfieldid"]);
		$revisiontextfield->setRevisionID($row["revisionid"]);
		$revisiontextfield->setValue($row["value"]);
		return $revisiontextfield;
	}

	public function getTextFieldID() {
		return $this->textfieldid;
	}

	public function setTextFieldID($textfieldid) {
		if ($this->textfieldid != $textfieldid) {
			$this->textfield = null;
		}
		$this->textfieldid = $textfieldid;
	}

	public function getTextField() {
		if ($this->textfield == null) {
			$this->textfield = $this->getStorage()->getMitgliedTextField($this->textfieldid);
		}
		return $this->textfield;
	}

	public function setTextField($textfield) {
		$this->setTextFieldID($textfield->getTextFieldID());
		$this->textfield = $textfield;
	}

	public function getRevisionID() {
		return $this->revisionid;
	}

	public function setRevisionID($revisionid) {
		if ($this->revisionid != $revisionid) {
			$this->revision = null;
		}
		$this->revisionid = $revisionid;
	}

	public function getRevision() {
		if ($this->revision == null) {
			$this->revision = $this->getStorage()->getMitgliederRevision($this->revisionid);
		}
		return $this->revision;
	}

	public function setRevision($revision) {
		$this->setRevisionID($revision->getRevisionID());
		$this->revision = $revision;
	}

	public function getValue() {
		return $this->value;
	}

	public function setValue($value) {
		$this->value = $value;
	}
}

?>
