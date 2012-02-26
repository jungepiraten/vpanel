<?php

require_once(VPANEL_CORE . "/storageobject.class.php");

class MitgliedTemplate extends StorageClass {
	private $templateid;
	private $label;
	private $gliederungid;
	private $mitgliedschaftid;
	private $beitrag;
	private $createmailtemplateid;

	private $gliederung;
	private $mitgliedschaft;
	private $createmailtemplate;

	public static function factory(Storage $storage, $row) {
		$mitgliedtemplate = new MitgliedTemplate($storage);
		$mitgliedtemplate->setMitgliedTemplateID($row["mitgliedtemplateid"]);
		$mitgliedtemplate->setLabel($row["label"]);
		$mitgliedtemplate->setGliederungID($row["gliederungid"]);
		$mitgliedtemplate->setMitgliedschaftID($row["mitgliedschaftid"]);
		$mitgliedtemplate->setBeitrag($row["beitrag"]);
		$mitgliedtemplate->setCreateMailTemplateID($row["createmail"]);
		return $mitgliedtemplate;
	}

	public function getMitgliedTemplateID() {
		return $this->templateid;
	}

	public function setMitgliedTemplateID($templateid) {
		$this->templateid = $templateid;
	} 

	public function getLabel() {
		return $this->label;
	}

	public function setLabel($label) {
		$this->label = $label;
	} 

	public function getGliederungID() {
		return $this->gliederungid;
	}

	public function setGliederungID($gliederungid) {
		if ($gliederungid != $this->gliederungid) {
			$this->gliederung = null;
		}
		$this->gliederungid = $gliederungid;
	}

	public function getGliederung() {
		if ($this->gliederung == null) {
			$this->gliederung = $this->getStorage()->getGliederung($this->gliederungid);
		}
		return $this->gliederung;
	}

	public function setGliederung($gliederung) {
		$this->setGliederungID($gliederung->getGliederungID());
		$this->gliederung = $gliederung;
	}

	public function getMitgliedschaftID() {
		return $this->mitgliedschaftid;
	}

	public function setMitgliedschaftID($mitgliedschaftid) {
		if ($mitgliedschaftid != $this->mitgliedschaftid) {
			$this->mitgliedschaft = null;
		}
		$this->mitgliedschaftid = $mitgliedschaftid;
	}

	public function getMitgliedschaft() {
		if ($this->mitgliedschaft == null) {
			$this->mitgliedschaft = $this->getStorage()->getMitgliedschaft($this->mitgliedschaftid);
		}
		return $this->mitgliedschaft;
	}

	public function setMitgliedschaft($mitgliedschaft) {
		$this->setMitgliedschaftID($mitgliedschaft->getMitgliedschaftID());
		$this->mitgliedschaft = $mitgliedschaft;
	}

	public function getBeitrag() {
		return $this->beitrag;
	}

	public function setBeitrag($beitrag) {
		$this->beitrag = $beitrag;
	}

	public function getCreateMailTemplateID() {
		return $this->createmailtemplateid;
	}

	public function setCreateMailTemplateID($createmailtemplateid) {
		if ($this->createmailtemplateid == $createmailtemplateid) {
			$this->createmailtemplate = null;
		}
		$this->createmailtemplateid = $createmailtemplateid;
	}

	public function getCreateMailTemplate() {
		if ($this->createmailtemplate == null) {
			$this->createmailtemplate = $this->getStorage()->getMailTemplate($this->createmailtemplateid);
		}
		return $this->createmailtemplate;
	}

	public function setCreateMailTemplate($createmailtemplate) {
		$this->setCreateMailTemplateID($createmailtemplate->getMailTemplateID());
		$this->createmailtemplate = $createmailtemplate;
	}

	public function save(Storage $storage = null) {
		if ($storage == null) {
			$storage = $this->getStorage();
		}
		$this->setMitgliedTemplateID($storage->setMitgliedTemplate(
			$this->getMitgliedTemplateID(),
			$this->getLabel(),
			$this->getGliederungID(),
			$this->getMitgliedschaftID(),
			$this->getBeitrag(),
			$this->getCreateMailTemplateID() ));
	}
}

?>
