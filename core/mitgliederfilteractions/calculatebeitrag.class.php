<?php

require_once(VPANEL_CORE . "/mitgliederfilteraction.class.php");
require_once(VPANEL_PROCESSES . "/mitgliederfiltercalculatebeitrag.class.php");

class CalculateBeitragMitgliederFilterAction extends MitgliederFilterAction {
	private $beitragid;
	private $gliederungsAnteil;

	public function __construct($actionid, $beitragid = null, $gliederungsAnteil = null) {
		parent::__construct($actionid);
		$this->beitragid = $beitragid;
		$this->gliederungsAnteil = $gliederungsAnteil;
	}

	public function getLabel() {
		return "Beitrag verteilen";
	}

	public function getPermission() {
		return "mitglieder_show";
	}

	protected function getStartTimestamp($session) {
		if ($session->hasVariable("starttimestamp")) {
			return strftime($session->getVariable("starttimestamp"));
		}
		return null;
	}

	protected function getEndTimestamp($session) {
		if ($session->hasVariable("endtimestamp")) {
			return strftime($session->getVariable("endtimestamp"));
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
			return array_map(create_function('$x', 'return $x/100;'), $session->getListVariable("gliederungsAnteil"));
		}
		return null;
	}

	public function execute($config, $session, $filter, $matcher) {
		$starttimestamp = $this->getStartTimestamp($session);
		$endtimestamp = $this->getEndTimestamp($session);
		$beitragid = $this->getBeitragID($session);
		$gliederungsAnteile = $this->getGliederungsAnteil($session);

		$beitraglist = null;
		if ($beitragid == null) {
			$beitraglist = $session->getStorage()->getBeitragList();
		}
		$gliederungen = null;
		if ($gliederungsAnteile == null) {
			$gliederungen = $session->getStorage()->getGliederungList($session->getAllowedGliederungIDs($this->getPermission()));
		}
		if ($starttimestamp == null || $endtimestamp == null || isset($beitraglist) || isset($gliederungen)) {
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
		             "gliederungshoehe" => $process->getGliederungsHoehe(),
		             "wunschhoehe" => $process->getWunschHoehe(),
		             "sumhoehe" => $process->getSumHoehe());
	}
}

?>
