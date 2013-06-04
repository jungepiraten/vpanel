<?php

require_once(VPANEL_CORE . "/mitgliederfilteraction.class.php");
require_once(VPANEL_PROCESSES . "/mitgliederfilterconsecutive.class.php");
require_once(VPANEL_PROCESSES . "/mitgliederfiltersendmail.class.php");
require_once(VPANEL_PROCESSES . "/mitgliederfilterdelete.class.php");

class DeleteMitgliederFilterAction extends MitgliederFilterAction {
	private $mailtemplateid;

	public function __construct($actionid, $label, $permission, $mailtemplateid = null) {
		parent::__construct($actionid, $label, $permission);
		$this->mailtemplateid = $mailtemplateid;
	}

	public function execute($config, $session, $filter, $matcher) {
		$mailtemplateid = $this->mailtemplateid;
		if ($session->hasVariable("mailtemplateid")) {
			$mailtemplateid = $session->getVariable("mailtemplateid");
		}

		$mailtemplate = null;
		if ($mailtemplateid != null) {
			$mailtemplate = $session->getStorage()->getMailTemplate($mailtemplateid);
		}

		if (! $session->hasVariable("timestamp") || ! $session->hasVariable("kommentar")) {
			return array("delete" => "options", "mailtemplate" => $mailtemplate, "mailtemplates" => $session->getStorage()->getMailTemplateList($this->getAllowedGliederungIDs($session)));
		} else {
			$timestamp = $session->getTimestampVariable("timestamp");

			$process = new MitgliederFilterConsecutiveProcess($session->getStorage());

			$p1 = new MitgliederFilterDeleteProcess($session->getStorage());
			$p1->setTimestamp($timestamp);
			$p1->setKommentar($session->getVariable("kommentar"));
			$process->addProcess($p1);

			if ($mailtemplate != null) {
				$p2 = new MitgliederFilterSendMailProcess($session->getStorage());
				$p2->setTemplate($mailtemplate);
				$process->addProcess($p2);
			}

			return $this->executeProcess($session, $process, $filter, $matcher);
		}
	}

	public function show($config, $session, $process) {
		return array("redirect" => $session->getLink("mitglieder"));
	}
}

?>
