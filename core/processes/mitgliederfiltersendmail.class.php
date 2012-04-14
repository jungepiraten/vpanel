<?php

require_once(VPANEL_PROCESSES . "/mitgliederfilter.class.php");

class MitgliederFilterSendMailProcess extends MitgliederFilterProcess {
	private $templateid;

	private $backend;
	private $template;

	public static function factory(Storage $storage, $row) {
		$process = parent::factory($storage, $row);
		$process->setBackend($row["backend"]);
		$process->setTemplateID($row["templateid"]);
		return $process;
	}

	public function getBackend() {
		return $this->backend;
	}

	public function setBackend($backend) {
		$this->backend = $backend;
	}

	public function getTemplateID() {
		return $this->templateid;
	}

	public function setTemplateID($templateid) {
		if ($templateid != $this->templateid) {
			$this->template = null;
		}
		$this->templateid = $templateid;
	}

	public function getTemplate() {
		if ($this->template == null) {
			$this->template = $this->getStorage()->getMailTemplate($this->getTemplateID());
		}
		return $this->template;
	}

	public function setTemplate($template) {
		$this->setTemplateID($template->getTemplateID());
		$this->template = $template;
	}
	
	protected function getData() {
		$data = parent::getData();
		$data["backend"] = $this->getBackend();
		$data["templateid"] = $this->getTemplateID();
		return $data;
	}

	protected function runProcessStep($mitglied) {
		$mail = $this->getTemplate()->generateMail($mitglied);
		$this->getBackend()->send($mail);
	}
}

?>
