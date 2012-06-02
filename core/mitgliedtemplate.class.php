<?php

require_once(VPANEL_CORE . "/aktion.class.php");

class MitgliedTemplate extends GliederungAktion {
	private $templateid;
	private $mitgliedschaftid;
	private $beitrag;
	private $createmailtemplateid;

	private $mitgliedschaft;
	private $createmailtemplate;

	public function __construct($templateid, $label, $permission, $gliederungid, $mitgliedschaftid, $beitrag, $createmailtemplateid) {
		parent::__construct($label, $permission, $gliederungid);
		$this->templateid = $templateid;
		$this->mitgliedschaftid = $mitgliedschaftid;
		$this->beitrag = $beitrag;
		$this->createmailtemplateid = $createmailtemplateid;
	}

	public function getMitgliedTemplateID() {
		return $this->templateid;
	}

	public function getMitgliedschaftID() {
		return $this->mitgliedschaftid;
	}

	public function getMitgliedschaft() {
		if ($this->mitgliedschaft == null) {
			$this->mitgliedschaft = $this->getStorage()->getMitgliedschaft($this->getMitgliedschaftID());
		}
		return $this->mitgliedschaft;
	}

	public function getBeitrag() {
		return $this->beitrag;
	}

	public function getCreateMailTemplateID() {
		return $this->createmailtemplateid;
	}

	public function getCreateMailTemplate() {
		if ($this->createmailtemplate == null) {
			$this->createmailtemplate = $this->getStorage()->getMailTemplate($this->getCreateMailTemplateID());
		}
		return $this->createmailtemplate;
	}
}

?>
