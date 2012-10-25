<?php

require_once(VPANEL_CORE . "/mitgliederfilteraction.class.php");
require_once(VPANEL_PROCESSES . "/mitgliederfiltercalculatebeitrag.class.php");

class CalculateBeitragMitgliederFilterAction extends MitgliederFilterAction {
	private $beitragid;
	private $gliederungsAnteil;

	public function __construct($actionid, $label, $permission, $beitragid = null, $gliederungsAnteil = null) {
		parent::__construct($actionid, $label, $permission);
		$this->beitragid = $beitragid;
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

	protected function getBeitragID($session) {
		if ($this->beitragid != null) {
			return $this->beitragid;
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
		$beitragid = $this->getBeitragID($session);
		$gliederungsAnteile = $this->getGliederungsAnteil($session);

		if ($starttimestamp == null || $endtimestamp == null || $beitragid == null || $gliederungsAnteile == null) {
			$beitraglist = $session->getStorage()->getBeitragList();
			$gliederungen = $gliederungen = $session->getStorage()->getGliederungList($session->getAllowedGliederungIDs($this->getPermission()));
			return array("calculatebeitrag" => "select", "beitraglist" => $beitraglist, "gliederungen" => $gliederungen);
		}

		$process = new MitgliederFilterCalculateBeitragProcess($session->getStorage());
		$process->setStartTimestamp($starttimestamp);
		$process->setEndTimestamp($endtimestamp);
		$process->setBeitragID($beitragid);
		$process->setGliederungsAnteile($gliederungsAnteile);
		return $this->executeProcess($session, $process, $filter, $matcher);
	}

	public function show($config, $session, $process) {
		$gliederungen = $session->getStorage()->getGliederungList($session->getAllowedGliederungIDs($this->getPermission()));
		return array("calculatebeitrag" => "summary",
		             "beitrag" => $process->getBeitrag(),
		             "gliederungen" => $gliederungen,
		             "anteile" => $process->getGliederungsAnteile(),
		             "gliederungsMitgliedHoehe" => $process->getGliederungsMitgliedHoehe(),
		             "gliederungsBeitragHoehe" => $process->getGliederungsBeitragHoehe(),
		             "sumhoehe" => $process->getSumHoehe());
	}
}

?>
