<?php

require_once(VPANEL_CORE . "/mitgliederfilteraction.class.php");
require_once(VPANEL_PROCESSES . "/mitgliederfiltercalculatebeitrag.class.php");

class CalculateBeitragMitgliederFilterAction extends MitgliederFilterAction {
	private $beitragids;
	private $userid;
	private $gliederungsAnteil;

	public function __construct($actionid, $label, $permission, $beitragids = null, $userid = null, $gliederungsAnteil = null) {
		parent::__construct($actionid, $label, $permission);
		$this->beitragids = $beitragids;
		$this->userid = $userid;
		$this->gliederungsAnteil = $gliederungsAnteil;
	}

	protected function getStartTimestamp($session) {
		if ($session->hasVariable("starttimestamp")) {
			return $session->getTimestampVariable("starttimestamp");
		}
		return null;
	}

	protected function getEndTimestamp($session) {
		if ($session->hasVariable("endtimestamp")) {
			return $session->getTimestampVariable("endtimestamp");
		}
		return null;
	}

	protected function getUserID($session) {
		if ($this->userid != null) {
			return $this->userid;
		}
		if ($session->hasVariable("userid")) {
			return $session->getVariable("userid");
		}
		return null;
	}

	protected function getBeitragIDs($session) {
		if ($this->beitragids != null) {
			return $this->beitragids;
		}
		if ($session->hasVariable("beitragid")) {
			return $session->getVariable("beitragid");
		}
		return null;
	}

	protected function getGliederungsAnteil($session) {
		if ($this->gliederungsAnteil != null) {
			return $this->gliederungsAnteil;
		} elseif ($session->hasVariable("gliederungsAnteil")) {
			$gliederungsAnteil = $session->getListVariable("gliederungsAnteil");
			array_walk_recursive($gliederungsAnteil, create_function('&$x, $key', '$x = $x/100;'));
			return $gliederungsAnteil;
		}
		return null;
	}

	public function execute($config, $session, $filter, $matcher) {
		$starttimestamp = $this->getStartTimestamp($session);
		$endtimestamp = $this->getEndTimestamp($session);
		$userid = $this->getUserID($session);
		$beitragids = $this->getBeitragIDs($session);
		$gliederungsAnteile = $this->getGliederungsAnteil($session);

		if ($starttimestamp == null || $endtimestamp == null || $beitragids == null || $gliederungsAnteile == null) {
			$userlist = $session->getStorage()->getUserList();
			$beitraglist = $session->getStorage()->getBeitragList();
			$gliederungen = $gliederungen = $session->getStorage()->getGliederungList($session->getAllowedGliederungIDs($this->getPermission()));
			return array("calculatebeitrag" => "select", "userlist" => $userlist, "beitraglist" => $beitraglist, "gliederungen" => $gliederungen);
		}

		$process = new MitgliederFilterCalculateBeitragProcess($session->getStorage());
		$process->setStartTimestamp($starttimestamp);
		$process->setEndTimestamp($endtimestamp);
		$process->setUserID($userid);
		$process->setBeitragIDs($beitragids);
		$process->setGliederungsAnteile($gliederungsAnteile);
		return $this->executeProcess($session, $process, $filter, $matcher);
	}

	public function show($config, $session, $process) {
		$gliederungen = $session->getStorage()->getGliederungList($session->getAllowedGliederungIDs($this->getPermission()));
		return array("calculatebeitrag" => "summary",
		             "gliederungen" => $gliederungen,
		             "anteile" => $process->getGliederungsAnteile(),
		             "gliederungsMitgliedHoehe" => $process->getGliederungsMitgliedHoehe(),
		             "gliederungsBeitragHoehe" => $process->getGliederungsBeitragHoehe(),
		             "sumhoehe" => $process->getSumHoehe());
	}
}

?>
