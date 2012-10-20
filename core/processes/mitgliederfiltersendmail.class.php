<?php

require_once(VPANEL_PROCESSES . "/mitgliederfilter.class.php");

class MitgliederFilterSendMailProcess extends MitgliederFilterProcess {
	private $templateid;

	private $backend;
	private $template;

	public static function factory(Storage $storage, $row) {
		$process = parent::factory($storage, $row);
		$process->setBackend($row["backend"]);
		$process->setTemplate($row["template"]);
		return $process;
	}

	public function getBackend() {
		return $this->backend;
	}

	public function setBackend($backend) {
		$this->backend = $backend;
	}

	public function getTemplate() {
		return $this->template;
	}

	public function setTemplate($template) {
		$this->template = $template;
	}

	protected function getData() {
		$data = parent::getData();
		$data["backend"] = $this->getBackend();
		$data["template"] = $this->getTemplate();
		return $data;
	}

	protected function runProcessStep($mitglied) {
		$mail = $this->getTemplate()->generateMail($mitglied);
		$this->getBackend()->send($mail);
	}
}

?>
