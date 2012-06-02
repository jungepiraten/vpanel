<?php

require_once(VPANEL_CORE . "/mitgliederfilteraction.class.php");
require_once(VPANEL_PROCESSES . "/mitgliederfiltersendmail.class.php");

class SendMailMitgliederFilterAction extends MitgliederFilterAction {
	private $mailtemplateid;

	public function __construct($actionid, $label, $permission, $mailtemplateid = null) {
		parent::__construct($actionid, $label, $permission);
		$this->mailtemplateid = $mailtemplateid;
	}

	protected function getMailTemplateID($session) {
		if ($this->mailtemplateid != null) {
			return $this->mailtemplateid;
		}
		if ($session->hasVariable("mailtemplateid")) {
			return $session->getVariable("mailtemplateid");
		}
		return null;
	}

	public function execute($config, $session, $filter, $matcher) {
		$mailtemplateid = $this->getMailTemplateID($session);
		if ($mailtemplateid == null) {
			$templates = $session->getStorage()->getMailTemplateList($session->getAllowedGliederungIDs("mitglieder_show"));
			return array("sendmail" => "select", "templates" => $templates);
		}

		switch ($session->getVariable("sendmail")) {
		default:
		case "preview":
			$mailtemplate = $session->getStorage()->getMailTemplate($mailtemplateid);

			$mitgliedercount = $session->getStorage()->getMitgliederCount($filter);
			$mitglied = array_shift($session->getStorage()->getMitgliederList($filter, 1, rand(0,$mitgliedercount-1)));
			$mail = $mailtemplate->generateMail($mitglied);

			return array("sendmail" => "preview", "mailtemplate" => $mailtemplate, "mail" => $mail);
		case "send":
			$process = new MitgliederFilterSendMailProcess($session->getStorage());
			$process->setBackend($config->getSendMailBackend());
			$process->setTemplateID($mailtemplateid);
			return $this->executeProcess($session, $process, $filter, $matcher);
		}
	}

	public function show($config, $session, $process) {
		return array("redirect" => $session->getLink("mitglieder"));
	}
}

?>
