<?php

require_once(VPANEL_CORE . "/process.class.php");

class MitgliederFilterSendMailProcess extends Process {
	private $templateid;

	private $filter;
	private $template;

	public static function factory(Storage $storage, $row) {
		$process = new MitgliederFilterSendMailProcess($storage);
		$process->setFilter($row["filter"]);
		$process->setTemplateID($row["templateid"]);
		return $process;
	}

	public function getFilterID() {
		return $this->getFilter()->getFilterID();
	}

	public function getFilter() {
		return $this->filter;
	}

	public function setFilter($filter) {
		$this->filter = $filter;
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
		return array("filter" => $this->getFilter(), "templateid" => $this->getTemplateID());
	}

	public function runProcess() {
		// TODO TADA!
		for ($i=0;$i<=1;$i+=0.05) {
			sleep(3);
			$this->setProgress($i);
			$this->save();
		}
		$this->setProgress(1);
		$this->save();
	}
}

?>
