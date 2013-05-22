<?php

class APITemplate {
	private $session;

	public function __construct($session) {
		$this->session = $session;
	}

	private function parseTimestamp($timestamp) {
		return strftime("%Y-%m-%d %T", $timestamp);
	}

	private function parseDatestamp($datestamp) {
		return strftime("%Y-%m-%d", $datestamp);
	}

	private function parseGliederung($gliederung) {
		$g = array();
		$g["gliederungid"] = $gliederung->getGliederungID();
		$g["label"] = $gliederung->getLabel();
		return $g;
	}

	private function parseUser($user) {
		$u = array();
		return $u;
	}

	protected function parseMitgliedschaft($mitgliedschaft) {
	        $m = array();
		$m["mitgliedschaftid"] = $mitgliedschaft->getMitgliedschaftID();
		$m["label"] = $mitgliedschaft->getLabel();
		$m["description"] = $mitgliedschaft->getDescription();
		return $m;
	}

	private function parseBeitrag($beitrag) {
		$b = array();
		$b["beitragid"] = $beitrag->getBeitragID();
		$b["label"] = $beitrag->getLabel();
		$b["hoehe"] = $beitrag->getHoehe();
		return $b;
	}

	private function parseMitgliedBeitragList($rows) {
		return array_map(array($this, 'parseMitgliedBeitrag'), $rows);
	}

	private function parseMitgliedBeitrag($mitgliedbeitrag) {
		$mb = array();
		$mb["mitgliedbeitragid"] = $mitgliedbeitrag->getMitgliederBeitragID();
		$mb["beitrag"] = $this->parseBeitrag($mitgliedbeitrag->getBeitrag());
		$mb["hoehe"] = $mitgliedbeitrag->getHoehe();
		$mb["buchungen"] = $this->parseMitgliedBeitragBuchungList($mitgliedbeitrag->getBuchungen());
		return $mb;
	}

	private function parseMitgliedBeitragBuchungList($rows) {
		return array_map(array($this, 'parseMitgliedBeitragBuchung'), $rows);
	}

	private function parseMitgliedBeitragBuchung($buchung) {
		$b = array();
		$b["buchungid"] = $buchung->getBuchungID();
		$b["gliederung"] = $this->parseGliederung($buchung->getGliederung());
		if ($buchung->getUser() != null) {
			$b["user"] = $this->parseUser($buchung->getUser());
		}
		if ($buchung->getTimestamp() != null) {
			$b["timestamp"] = $this->parseTimestamp($buchung->getTimestamp());
		}
		$b["hoehe"] = $buchung->getHoehe();
		return $b;
	}

	private function parseMitgliedRevision($revision) {
		$r = array();
		$r["gliederung"] = $this->parseGliederung($revision->getGliederung());
		$r["mitgliedschaft"] = $this->parseMitgliedschaft($revision->getMitgliedschaft());
		if ($revision->isNatPerson()) {
			$r["natperson"] = $this->parseNatPerson($revision->getNatPerson());
		}
		if ($revision->isJurPerson()) {
			$r["jurperson"] = $this->parseJurPerson($revision->getJurPerson());
		}
		return $r;
	}

	private function parseNatPerson($natperson) {
		$row = array();
		$row["natpersonid"] = $natperson->getNatPersonID();
		$row["anrede"] = $natperson->getAnrede();
		$row["vorname"] = $natperson->getVorname();
		$row["name"] = $natperson->getName();
		$row["geburtsdatum"] = $natperson->getGeburtsdatum();
		$row["nationalitaet"] = $natperson->getNationalitaet();
		return $row;
	}

	private function parseJurPerson($jurperson) {
		$row = array();
		$row["jurpersonid"] = $jurperson->getJurPersonID();
		$row["label"] = $jurperson->getLabel();
		return $row;
	}

	private function parseMitglied($mitglied) {
		$m = array();
		$m["mitgliedid"] = $mitglied->getMitgliedID();
		$m["eintritt"] = $this->parseDatestamp($mitglied->getEintrittsdatum());
		if ($mitglied->isAusgetreten()) {
			$row["austritt"] = $this->parseDatestamp($mitglied->getAustrittsdatum());
		}
		$m["beitraege"] = $this->parseMitgliedBeitragList($mitglied->getBeitragList());
		$m["latest"] = $this->parseMitgliedRevision($mitglied->getLatestRevision());
		return $m;
	}

	private function parseResult($result) {
		if (is_array($result)) {
			return array_map(array($this, "parseResult"), $result);
		} else if ($result instanceof Gliederung) {
			return $this->parseGliederung($result);
		} else if ($result instanceof User) {
			return $this->parseUser($result);
		} else if ($result instanceof Mitglied) {
			return $this->parseMitglied($result);
		}
	}

	public function output($result = null, $httpcode = 200) {
		$data = array();
		if ($this->session->isActive()) {
			$data["sessionid"] = $this->session->getSessionID();
			$data["challenge"] = $this->session->getChallenge();
		}
		if ($result != null) {
			$data["result"] = $this->parseResult($result);
		}
		header("Status: " . $httpcode, true, $httpcode);
		print(json_encode($data));
	}
}

?>
