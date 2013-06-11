<?php

require_once(VPANEL_TEXTREPLACER . "/variable.class.php");

class MitgliedTextReplacer extends VariableTextReplacer {
	private $mitglied;

	public function __construct($mitglied) {
		$this->mitglied = $mitglied;
	}

	protected function getVariableValue($keyword) {
		$revision = $this->mitglied->getLatestRevision();
		$kontakt = $revision->getKontakt();

		if (substr(strtoupper($keyword),0,7) == "BEITRAG") {
			$tuple = explode(".", $keyword, 2);
			if (isset($tuple[1])) {
				foreach ($this->mitglied->getBeitragList() as $beitrag) {
					if (strtoupper($beitrag->getBeitrag()->getLabel()) == strtoupper($tuple[1])) {
						return $beitrag->getHoehe();
					}
				}
			} else {
				return $revision->getBeitrag();
			}
		}
		if (strtoupper(substr($keyword,0,5)) == "TOKEN") {
			list($k, $token) = explode(".", $keyword, 2);
			return hash_hmac("md5", $this->mitglied->getMitgliedID(), $token);
		}

		// If we do this earlier we get crappy tokens
		$keyword = strtoupper($keyword);
		switch ($keyword) {
		case "MITGLIEDID":
			return $this->mitglied->getMitgliedID();
		case "EINTRITT":
			return date("d.m.Y", $this->mitglied->getEintrittsdatum());
		case "AUSTRITT":
			if ($this->mitglied->getAustrittsdatum() == null) {
				return "";
			}
			return date("d.m.Y", $this->mitglied->getAustrittsdatum());
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
		case "BOUNCES-TOTAL":
			return count($kontakt->getEMail()->getBounces());
		case "BOUNCES-NEW":
			return count($kontakt->getEMail()->getNewBounces());
		case "IBAN":
			if ($kontakt->hasKonto()) {
				return $kontakt->getKonto()->getIBan();
			}
			return "";
		case "MITGLIEDSCHAFT":
			return $revision->getMitgliedschaft()->getLabel();
		case "SCHULDEN":
			return $this->mitglied->getSchulden();
		case "BEZEICHNUNG":
			return $revision->getBezeichnung();
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
			case "FIRMA":
				return $jurperson->getLabel();
			}
		}
		return null;
	}
}
