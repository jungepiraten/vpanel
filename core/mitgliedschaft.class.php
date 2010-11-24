<?php

require_once(VPANEL_CORE . "/storageobject.class.php");
require_once(VPANEL_CORE . "/mailtemplate.class.php");

class Mitgliedschaft extends StorageClass {
	private $mitgliedschaftid;
	private $label;
	private $description;
	private $defaultbeitrag;
	private $defaultcreatemailid;

	private $defaultcreatemail;

	public static function factory(Storage $storage, $row) {
		$mitgliedschaft = new Mitgliedschaft($storage);
		$mitgliedschaft->setMitgliedschaftID($row["mitgliedschaftid"]);
		$mitgliedschaft->setLabel($row["label"]);
		$mitgliedschaft->setDescription($row["description"]);
		$mitgliedschaft->setDefaultBeitrag($row["defaultbeitrag"]);
		$mitgliedschaft->setDefaultCreateMailID($row["defaultcreatemail"]);
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

	public function getDefaultBeitrag() {
		return $this->defaultbeitrag;
	}

	public function setDefaultBeitrag($defaultbeitrag) {
		$this->defaultbeitrag = $defaultbeitrag;
	}

	public function getDefaultCreateMail() {
		if ($this->defaultcreatemail == null) {
			$this->defaultcreatemail = $this->getStorage()->getMailTemplate($this->getDefaultCreateMailID());
		}
		return $this->defaultcreatemail;
	}

	public function getDefaultCreateMailID() {
		return $this->defaultcreatemailid;
	}

	public function setDefaultCreateMail($mailtemplate) {
		$this->setDefaultCreateMailID($mailtemplate->getMailTemplateID());
		$this->mailtemplate = $mailtemplate;
	}

	public function setDefaultCreateMailID($mailtemplateid) {
		if ($this->mailtemplateid != $mailtemplateid) {
			$this->mailtemplate = null;
		}
		$this->defaultcreatemailid = $mailtemplateid;
	}

	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$this->setMitgliedschaftID( $storage->setMitgliedschaft(
			$this->getMitgliedschaftID(),
			$this->getLabel(),
			$this->getDescription(),
			$this->getDefaultBeitrag(),
			$this->getDefaultCreateMailID() ));
	}
}

?>
