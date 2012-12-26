<?php

require_once(VPANEL_CORE . "/aktion.class.php");

abstract class DokumentTransition extends GliederungAktion {
	private $transitionid;
	private $kategorieid;
	private $statusid;

	public function __construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid) {
		parent::__construct($label, $permission, $gliederungid);
		$this->transitionid = $transitionid;
		$this->kategorieid = $kategorieid;
		$this->statusid = $statusid;
	}

	public function getDokumentTransitionID() {
		return $this->transitionid;
	}

	public function getDokumentKategorieID() {
		return $this->kategorieid;
	}

	public function getDokumentStatusID() {
		return $this->statusid;
	}

	abstract public function getNextKategorieID($session);
	abstract public function getNextStatusID($session);
	abstract public function getKommentar($session);

	public function isMatching($session, $kategorieid, $statusid) {
		if ( ($this->isAllowed($session)) &&
		     ($this->getDokumentKategorieID() == null || $this->getDokumentKategorieID() == $kategorieid) &&
		     ($this->getDokumentStatusID() == null || $this->getDokumentStatusID() == $statusid) ) {
			return true;
		}
		return false;
	}

	protected function executeProcess($session, $process, $filter, $matcher) {
		$process->setMatcher($matcher);
		$process->setUser($session->getUser());
		$process->setNextKategorieID($this->getNextKategorieID($session));
		$process->setNextStatusID($this->getNextStatusID($session));
		$process->setKommentar($this->getKommentar($session));
		// Zwischenspeichern, um ProzessID zu erhalten
		$process->save();

		// Viele Prozesse finden erst zur Laufzeit ihr Ziel
		if ($process->getFinishedPage() == null) {
			$process->setFinishedPage($session->getLink("dokumente_transitionprocess", $this->getDokumentTransitionID(), $process->getProcessID()));
			$process->save();
		}

		if ($session->getStorage()->getDokumentCount($matcher) < 5) {
			$process->run();
			return array("redirect" => $process->getFinishedPage());
		} else {
			return array("redirect" => $session->getLink("processes_view", $process->getProcessID()));
		}
	}

	abstract public function execute($config, $session, $filter, $matcher);
	public function show($config, $session, $process) {
		return array("redirect" => $session->getLink("dokumente"));
	}
}

abstract class StaticDokumentTransition extends DokumentTransition {
	private $nextkategorieid;
	private $nextstatusid;
	private $kommentar;

	public function __construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $kommentar) {
		parent::__construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid);
		$this->nextkategorieid = $nextkategorieid;
		$this->nextstatusid = $nextstatusid;
		$this->kommentar = $kommentar;
	}

	public function getNextKategorieID($session) {
		return $this->nextkategorieid;
	}

	public function getNextStatusID($session) {
		return $this->nextstatusid;
	}

	public function getKommentar($session) {
		return $this->kommentar;
	}
}

interface SingleDokumentTransition {}

interface MultiDokumentTransition {}

?>
