<?php

require_once(VPANEL_CORE . "/process.class.php");

class MitgliederFilterSendMailProcess extends Process {
	private $templateid;

	private $backend;
	private $filter;
	private $template;

	public static function factory(Storage $storage, $row) {
		$process = new MitgliederFilterSendMailProcess($storage);
		$process->setBackend($row["backend"]);
		$process->setFilter($row["filter"]);
		$process->setTemplateID($row["templateid"]);
		return $process;
	}

	public function getBackend() {
		return $this->backend;
	}

	public function setBackend($backend) {
		$this->backend = $backend;
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
		return array("backend" => $this->getBackend(), "filter" => $this->getFilter(), "templateid" => $this->getTemplateID());
	}

	public function runProcess() {
		$result = $this->getStorage()->getMitgliederResult($this->getFilter());
		$max = $result->getCount();
		$i = 0;
		$stepwidth = min(1, ceil(100 / max(1,$max)));

		while ($mitglied = $result->fetchRow()) {
			$mail = $this->getTemplate()->generateMail($mitglied);
			$this->getBackend()->send($mail);
			
			if ((++$i % $stepwidth) == 0) {
				$this->setProgress($i / $max);
				$this->save();
			}
		}
		
		$this->setProgress(1);
		$this->save();
	}
}

?>
