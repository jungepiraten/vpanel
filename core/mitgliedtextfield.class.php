<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class MitgliedTextField extends StorageClass {
	private $textfieldid;
	private $label;

	public static function factory(Storage $storage, $row) {
		$textfield = new MitgliedTextField($storage);
		$textfield->setTextFieldID($row["textfieldid"]);
		$textfield->setLabel($row["label"]);
		return $textfield;
	}

	public function getTextFieldID() {
		return $this->textfieldid;
	}

	public function setTextFieldID($textfieldid) {
		$this->textfieldid = $textfieldid;
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
		$this->setTextFieldID( $storage->setMitgliedTextField(
			$this->getTextFieldID(),
			$this->getLabel() ));
	}
}

?>
