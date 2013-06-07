<?php

require_once(VPANEL_PROCESSES . "/mitgliederfilter.class.php");
require_once(VPANEL_TEXTREPLACER . "/mitglied.class.php");

class MitgliederFilterSendMailProcess extends MitgliederFilterProcess {
	private $templateid;

	private $template;

	public static function factory(Storage $storage, $row) {
		$process = parent::factory($storage, $row);
		$process->setTemplate($row["template"]);
		return $process;
	}

	public function getTemplate() {
		return $this->template;
	}

	public function setTemplate($template) {
		$this->template = $template;
	}

	protected function getData() {
		$data = parent::getData();
		$data["template"] = $this->getTemplate();
		return $data;
	}

	protected function runProcessStep($mitglied) {
		$replacer = new MitgliedTextReplacer($mitglied);
		$mail = $this->getTemplate()->generateMail($mitglied->getLatestRevision()->getKontakt()->getEMail(), $replacer);
		$mail->send();
	}
}

?>
