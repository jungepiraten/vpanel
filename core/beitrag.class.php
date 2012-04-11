<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class Beitrag extends StorageClass {
	private $beitragid;
	private $label;
	private $hoehe;
	private $mailtemplateid;

	private $mailtemplate;

	public static function factory(Storage $storage, $row) {
		$beitrag = new Beitrag($storage);
		$beitrag->setBeitragID($row["beitragid"]);
		$beitrag->setLabel($row["label"]);
		$beitrag->setHoehe($row["hoehe"]);
		$beitrag->setMailTemplateID($row["mailtemplateid"]);
		return $beitrag;
	}

	public function getBeitragID() {
		return $this->beitragid;
	}

	public function setBeitragID($beitragid) {
		$this->beitragid = $beitragid;
	}

	public function getLabel() {
		return $this->label;
	}

	public function setLabel($label) {
		$this->label = $label;
	}

	public function getHoehe() {
		return $this->hoehe;
	}

	public function setHoehe($hoehe) {
		$this->hoehe = $hoehe;
	}

	public function getMailTemplateID() {
		return $this->mailtemplateid;
	}

	public function setMailTemplateID($mailtemplateid) {
		if ($mailtemplateid != $this->mailtemplateid) {
			$this->mailtemplate = null;
		}
		$this->mailtemplateid = $mailtemplateid;
	}

	public function getMailTemplate() {
		if ($this->mailtemplate == null && $this->getMailTemplateID() != null) {
			$this->mailtemplate = $this->getStorage()->getMailTemplate($this->getMailTemplateID());
		}
		return $this->mailtemplate;
	}

	public function setMailTemplate($mailtemplate) {
		$this->setMailTemplateID($mailtemplate->getTemplateID());
		$this->mailtemplate = $mailtemplate;
	}

	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$this->setBeitragID( $storage->setBeitrag(
			$this->getBeitragID(),
			$this->getLabel(),
			$this->getHoehe(),
			$this->getMailTemplateID() ));
	}

	public function delete(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		foreach ($storage->getMitgliederBeitragBuchungByBeitragList($this->getBeitragID()) as $mitgliederbeitrag) {
			$mitgliederbeitrag->delete();
		}
		$storage->delBeitrag($this->getBeitragID());
	}
}

?>
