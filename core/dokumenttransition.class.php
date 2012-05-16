<?php

abstract class DokumentTransition {
	private $transitionid;
	private $hidden;
	private $gliederungid;
	private $kategorieid;
	private $statusid;
	private $nextkategorieid;
	private $nextstatusid;
	private $notizkommentar;

	public function __construct($transitionid, $hidden, $gliederungid, $kategorieid, $statusid, $nextkategorieid, $nextstatusid, $notizkommentar) {
		$this->transitionid = $transitionid;
		$this->hidden = $hidden;
		$this->gliederungid = $gliederungid;
		$this->kategorieid = $kategorieid;
		$this->statusid = $statusid;
		$this->nextkategorieid = $nextkategorieid;
		$this->nextstatusid = $nextstatusid;
		$this->notizkommentar = $notizkommentar;
	}

	public function getDokumentTransitionID() {
		return $this->transitionid;
	}

	public function isHidden() {
		return $this->hidden;
	}

	abstract public function getLabel();

	abstract public function getPermission();

	public function getGliederungID() {
		return $this->gliederungid;
	}
	
	public function getDokumentKategorieID() {
		return $this->kategorieid;
	}
	
	public function getDokumentStatusID() {
		return $this->statusid;
	}

	public function getNextKategorieID() {
		return $this->nextkategorieid;
	}

	public function getNextStatusID() {
		return $this->nextstatusid;
	}

	public function getNotizKommentar() {
		return $this->notizkommentar;
	}

	public function isMatching($gliederungids, $kategorieid, $statusid) {
		if ( ($this->getGliederungID() == null || in_array($this->getGliederungID(), $gliederungids)) &&
		     ($this->getDokumentKategorieID() == null || $this->getDokumentKategorieID() == $kategorieid) &&
		     ($this->getDokumentStatusID() == null || $this->getDokumentStatusID() == $statusid) ) {
			return true;
		}
		return false;
	}

	protected function executeProcess($session, $process) {
		$process->setUser($session->getUser());
		$process->setNextKategorieID($this->getNextKategorieID());
		$process->setNextStatusID($this->getNextStatusID());
		$process->setNotizKommentar($this->getNotizKommentar());
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

	abstract public function show($config, $session, $process);
}

interface SingleDokumentTransition {
	public function execute($config, $session, $dokumentid);
}

interface MultiDokumentTransition {
	public function executeMulti($config, $session, $gliederungid, $kategorieid, $statusid);
}

?>
