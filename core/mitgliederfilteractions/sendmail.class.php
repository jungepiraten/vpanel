<?php

require_once(VPANEL_CORE . "/mailtemplate.class.php");
require_once(VPANEL_CORE . "/mitgliederfilteraction.class.php");
require_once(VPANEL_TEXTREPLACER . "/mitglied.class.php");
require_once(VPANEL_PROCESSES . "/mitgliederfiltersendmail.class.php");

class SendMailMitgliederFilterAction extends MitgliederFilterAction {
	private $mailtemplateid;

	public function __construct($actionid, $label, $permission, $mailtemplate = null) {
		parent::__construct($actionid, $label, $permission);
		$this->mailtemplate = $mailtemplate;
	}

	protected function getMailTemplate($session) {
		if ($this->mailtemplate != null) {
			return $this->mailtemplate;
		}
		if ($session->hasVariable("mailtemplatecode")) {
			$mailtemplate = unserialize(base64_decode($session->getVariable("mailtemplatecode")));
			$mailtemplate->setStorage($session->getStorage());
			return $mailtemplate;
		}
		if ($session->hasVariable("body")) {
			$mailtemplate = new MailTemplate($session->getStorage());
			$headers = array_combine($session->getListVariable("headerfields"), $session->getListVariable("headervalues"));
			foreach ($headers as $field => $value) {
				if (!empty($field)) {
					$mailtemplate->setHeader($field, $value);
				}
			}
			$mailtemplate->setBody($session->getVariable("body"));
			return $mailtemplate;
		}
		return null;
	}

	public function execute($config, $session, $filter, $matcher) {
		$mailtemplate = $this->getMailTemplate($session);
		if ($mailtemplate == null || $session->hasVariable("form")) {
			$templates = $session->getStorage()->getMailTemplateList($session->getAllowedGliederungIDs("mitglieder_show"));
			return array("sendmail" => "select", "templates" => $templates, "mailtemplate" => $mailtemplate);
		}

		// Remove Storage before (less driver-headaches and space-consumption)
		$mailtemplatecode = "";
		if ($mailtemplate != null) {
			$a = clone $mailtemplate;
			$a->setStorage(null);
			$mailtemplatecode = base64_encode(serialize($a));
		}

		switch ($session->getVariable("sendmail")) {
		default:
		case "preview":
			$mitgliedercount = $session->getStorage()->getMitgliederCount($filter);
			$mitglied = array_shift($session->getStorage()->getMitgliederList($filter, 1, rand(0,$mitgliedercount-1)));
			$replacer = new MitgliedTextReplacer($mitglied);
			$mail = $mailtemplate->generateMail($mitglied->getLatestRevision()->getKontakt()->getEMail(), $replacer);

			return array("sendmail" => "preview", "mailtemplatecode" => $mailtemplatecode, "mail" => $mail);
		case "send":
			$process = new MitgliederFilterSendMailProcess($session->getStorage());
			$process->setTemplate($mailtemplate);
			return $this->executeProcess($session, $process, $filter, $matcher);
		}
	}

	public function show($config, $session, $process) {
		return array("redirect" => $session->getLink("mitglieder"));
	}
}

?>
