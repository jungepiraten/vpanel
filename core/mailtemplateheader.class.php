<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class MailTemplateHeader extends StorageClass {
	private $templateid;
	private $field;
	private $value;

	public static function factory(Storage $storage, $row) {
		$header = new MailTemplateHeader($storage);
		$header->setTemplateID($row["templateid"]);
		$header->setField($row["field"]);
		$header->setValue($row["value"]);
		return $header;
	}

	public function __construct(Storage $storage, $templateid = null, $field = null, $value = null) {
		parent::__construct($storage);
		if ($templateid != null) {
			$this->setTemplateID($templateid);
		}
		if ($field != null) {
			$this->setField($field);
		}
		if ($value != null) {
			$this->setValue($value);
		}
	}

	public function getTemplateID() {
		return $this->templateid;
	}

	public function setTemplateID($templateid) {
		$this->templateid = $templateid;
	}

	public function getField() {
		return $this->field;
	}

	public function setField($field) {
		$this->field = $field;
	}

	public function getValue() {
		return $this->value;
	}

	public function setValue($value) {
		$this->value = $value;
	}
}

?>
