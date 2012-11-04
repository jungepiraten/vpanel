<?php

require_once(VPANEL_CORE . "/globalobject.class.php");
require_once(VPANEL_CORE . "/mitgliedrevision.class.php");

class Mitglied extends GlobalClass {
	private $mitgliedid;
	private $eintrittsdatum;
	private $austrittsdatum;

	private $beitraglist;

	private $revisions;
	private $latestRevision;

	public static function factory(Storage $storage, $row) {
		$mitglied = new Mitglied($storage);
		$mitglied->setMitgliedID($row["mitgliedid"]);
		$mitglied->setGlobalID($row["globalid"]);
		$mitglied->setEintrittsdatum($row["eintritt"]);
		$mitglied->setAustrittsdatum($row["austritt"]);
		return $mitglied;
	}

	public function getMitgliedID() {
		return $this->mitgliedid;
	}

	public function setMitgliedID($mitgliedid) {
		$this->mitgliedid = $mitgliedid;
	}

	public function getEintrittsdatum() {
		return $this->eintrittsdatum;
	}

	public function setEintrittsdatum($eintrittsdatum) {
		$this->eintrittsdatum = $eintrittsdatum;
	}

	public function getAustrittsdatum() {
		return $this->austrittsdatum;
	}

	public function setAustrittsdatum($austrittsdatum) {
		$this->austrittsdatum = $austrittsdatum;
	}

	public function isAusgetreten() {
		return $this->austrittsdatum != null;
	}

	public function isMitglied() {
		return !$this->isAusgetreten();
	}

	public function getRevisionList() {
		if ($this->revisions === null) {
			$this->revisions = array();
			foreach ($this->getStorage()->getMitgliederRevisionsByMitgliedIDList($this->getMitgliedID()) as $revision) {
				$this->addRevision($revision);
			}
		}
		return $this->revisions;
	}

	public function &getRevision($revisionid) {
		$this->getRevisionList();
		if (!isset($this->revisions[$revisionid]) or $this->revisions[$revisionid] == null) {
			$this->revisions[$revisionid] = $this->getStorage()->getMitgliederRevision($revisionid);
		}
		return $this->revisions[$revisionid];
	}

	public function addRevision($revision) {
		$this->getRevisionList();
		$this->revisions[$revision->getRevisionID()] = $revision;
		if (!isset($this->latestRevision) || $revision->getTimestamp() > $this->latestRevision->getTimestamp()) {
			$this->latestRevision = $revision;
		}
	}

	public function getLatestRevision() {
		if (!isset($this->latestRevision)) {
			$this->latestRevision = $this->getRevision(end($this->getRevisionIDs()));
		}
		return $this->latestRevision;
	}

	public function setLatestRevision($revision) {
		$this->latestRevision = $revision;
	}

	public function getRevisionIDs() {
		return array_map(create_function('$a', 'return $a->getRevisionID();'), $this->getRevisionList());
	}

	public function getBeitragList() {
		if ($this->beitraglist === null) {
			$this->beitraglist = array();
			foreach ($this->getStorage()->getMitgliederBeitragByMitgliedList($this->getMitgliedID()) as $beitrag) {
				$this->beitraglist[$beitrag->getBeitragID()] = $beitrag;
			}
		}
		return $this->beitraglist;
	}

	public function getBeitrag($beitragid) {
		$this->getBeitragList();
		if (!isset($this->beitraglist[$beitragid])) {
			$this->beitraglist[$beitragid] = new MitgliedBeitrag($this->getStorage());
			$this->beitraglist[$beitragid]->setMitglied($this);
			$this->beitraglist[$beitragid]->setBeitragID($beitragid);
		}
		return $this->beitraglist[$beitragid];
	}

	public function delBeitrag($beitragid) {
		$this->getBeitragList();
		unset($this->beitraglist[$beitragid]);
	}

	public function save(Storage $storage = null) {
		if ($storage === null) {
			$storage = $this->getStorage();
		}
		$this->setMitgliedID( $storage->setMitglied(
			$this->getMitgliedID(),
			$this->getGlobalID(),
			$this->getEintrittsdatum(),
			$this->getAustrittsdatum() ));
		// TODO revisions speichern ?

		if ($this->beitraglist !== null) {
			foreach ($this->beitraglist as $beitrag) {
				$beitrag->save($storage);
			}
		}
	}

	private function getVariableValue($keyword) {
		$keyword = strtoupper($keyword);

		$revision = $this->getLatestRevision();
		$kontakt = $revision->getKontakt();

		if (substr($keyword,0,7) == "BEITRAG") {
			$tuple = explode(".", $keyword, 2);
			if (isset($tuple[1])) {
				foreach ($this->getBeitragList() as $beitrag) {
					if ($beitrag->getBeitrag()->getLabel() == $tuple[1]) {
						return $beitrag->getHoehe();
					}
				}
			} else {
				return $revision->getBeitrag();
			}
		}
		switch ($keyword) {
		case "MITGLIEDID":
			return $this->getMitgliedID();
		case "EINTRITT":
			return date("d.m.Y", $this->getEintrittsdatum());
		case "AUSTRITT":
			if ($this->getAustrittsdatum() == null) {
				return "";
			}
			return date("d.m.Y", $this->getAustrittsdatum());
		case "GLIEDERUNG":
			return $revision->getGliederung()->getLabel();
		case "ADRESSZUSATZ":
			return $kontakt->getAdresszusatz();
		case "STRASSE":
			return $kontakt->getStrasse();
		case "HAUSNUMMER":
			return $kontakt->getHausnummer();
		case "PLZ":
			return $kontakt->getOrt()->getPLZ();
		case "ORT":
			return $kontakt->getOrt()->getLabel();
		case "STATE":
			return $kontakt->getOrt()->getState()->getLabel();
		case "COUNTRY":
			return $kontakt->getOrt()->getState()->getCountry()->getLabel();
		case "TELEFONNUMMER":
			return $kontakt->getTelefonnummer();
		case "HANDYNUMMER":
			return $kontakt->getHandynummer();
		case "EMAIL":
			return $kontakt->getEMail()->getEMail();
		case "MITGLIEDSCHAFT":
			return $revision->getMitgliedschaft()->getLabel();
		}
		if ($revision->isNatPerson()) {
			$natperson = $revision->getNatPerson();
			if (substr($keyword, 0, 5) == "ALTER") {
				$tuple = explode(".", $keyword, 2);
				$timestamp = time();
				if (count($tuple) >= 2) {
					$timestamp = mktime(0,0,0, substr($tuple[1],3,2), substr($tuple[1],5,2), substr($tuple[1],0,4));
				}
				return $natperson->getAlter($timestamp);
			}
			switch ($keyword) {
			case "BEZEICHNUNG":
				return $natperson->getAnrede() . " " . $natperson->getVorname() . " " . $natperson->getName();
			case "ANREDE":
				return $natperson->getAnrede();
			case "NAME":
				return $natperson->getName();
			case "VORNAME":
				return $natperson->getVorname();
			case "GEBURTSDATUM":
				return date("d.m.Y", $natperson->getGeburtsdatum());
			case "NATIONALITAET":
				return $natperson->getNationalitaet();
			}
		}
		if ($revision->isJurPerson()) {
			$jurperson = $revision->getJurPerson();
			switch ($keyword) {
			case "BEZEICHNUNG":
				return $jurperson->getLabel();
			case "FIRMA":
				return $jurperson->getLabel();
			}
		}
		return "";
	}

	public function replaceText($text) {
		// Suche alle vorkommenden Variablen ab
		preg_match_all('/\\{(.*?)\\}/', $text, $matches);
		$keywords = array_unique($matches[1]);
		foreach ($keywords as $keyword) {
			$text = str_replace("{" . $keyword . "}", $this->getVariableValue($keyword), $text);
		}
		return $text;
	}
}

?>
