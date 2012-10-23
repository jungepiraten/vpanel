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
	abstract public function getNotizKommentar($session);

	public function isMatching($session, $kategorieid, $statusid) {
		if ( ($this->isAllowed($session)) &&
		     ($this->getDokumentKategorieID() == null || $this->getDokumentKategorieID() == $kategorieid) &&
		     ($this->getDokumentStatusID() == null || $this->getDokumentStatusID() == $statusid) ) {
			return true;
		}
		return false;
	}

	protected function executeProcess($session, $process) {
		$process->setUser($session->getUser());
		$process->setNextKategorieID($this->getNextKategorieID($session));
		$process->setNextStatusID($this->getNextStatusID($session));
		$process->setNotizKommentar($this->getNotizKommentar($session));
		// Zwischenspeichern, um ProzessID zu erhalten
		$process->save();

		// Viele Prozesse finden erst zur Laufzeit ihr Ziel
		if ($process->getFinishedPage() == null) {
			$process->setFinishedPage($session->getLink("dokumente_transitionprocess", $this->getDokumentTransitionID(), $process->getProcessID()));
			$process->save();
		}

		// TODO single-process direkt abfackeln
		return array("redirect" => $session->getLink("processes_view", $process->getProcessID()));
	}

	public function show($config, $session, $process) {
		if ($process->getDokumentID() != null) {
			return array("redirect" => $session->getLink("dokumente_details", $process->getDokumentID()));
		} else {
			return array("redirect" => $session->getLink("dokumente_page", null, $process->getDokumentKategorieID(), $process->getDokumentStatusID(), 0));
		}
	}
}

abstract class StaticDokumentTransition extends DokumentTransition {
	private $nextkategorieid;
	private $nextstatusid;
	private $notizkommentar;

	public function __construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $notizkommentar) {
		parent::__construct($transitionid, $label, $permission, $gliederungid, $kategorieid, $statusid);
		$this->nextkategorieid = $nextkategorieid;
		$this->nextstatusid = $nextstatusid;
		$this->notizkommentar = $notizkommentar;
	}

	public function getNextKategorieID($session) {
		return $this->nextkategorieid;
	}

	public function getNextStatusID($session) {
		return $this->nextstatusid;
	}

	public function getNotizKommentar($session) {
		return $this->notizkommentar;
	}
}

interface SingleDokumentTransition {
	public function execute($config, $session, $dokumentid);
}

interface MultiDokumentTransition {
	public function executeMulti($config, $session, $gliederungid, $kategorieid, $statusid);
}

?>
