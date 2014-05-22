<?php

require_once(VPANEL_DOKUMENTTEMPLATES . "/person.class.php");

class MitgliedCreateDokumentTemplate extends NatPersonDokumentTemplate {
	private function getAdresszusatz($session) {
		return $session->getVariable("adresszusatz");
	}

	private function getStrasse($session) {
		return $session->getVariable("strasse");
	}

	private function getHausnummer($session) {
		return $session->getVariable("hausnummer");
	}

	private function getPLZ($session) {
		return $session->getVariable("plz");
	}

	private function getOrt($session) {
		return $session->getVariable("ort");
	}

	private function getTelefonnummer($session) {
		return $session->getVariable("telefon");
	}

	private function getHandynummer($session) {
		return $session->getVariable("handy");
	}

	private function getEMailAdresse($session) {
		return $session->getVariable("email");
	}

	private function getIBan($session) {
		return $session->getVariable("iban");
	}

	private function getBIC($session) {
		return $session->getVariable("bic");
	}

	private function getBeitrag($session) {
		return $session->getVariable("beitrag");
	}

	private function getBeitragTimeFormatID($session) {
		return $session->getIntVariable("beitragtimeformatid");
	}

	private function getFlags($session) {
		return array_keys($session->getListVariable("flags"));
	}

	private function getTextFields($session) {
		return $session->getListVariable("textfields");
	}

	public function getDokumentData($session) {
		$array = parent::getDokumentData($session);
		$array["addresszusatz"] = $this->getAdresszusatz($session);
		$array["strasse"] = $this->getStrasse($session);
		$array["hausnummer"] = $this->getHausnummer($session);
		$array["plz"] = $this->getPLZ($session);
		$array["ort"] = $this->getOrt($session);
		$array["telefon"] = $this->getTelefonnummer($session);
		$array["handy"] = $this->getHandynummer($session);
		$array["email"] = $this->getEMailAdresse($session);
		$array["iban"] = $this->getIBan($session);
		$array["bic"] = $this->getBIC($session);
		$array["beitrag"] = $this->getBeitrag($session);
		$array["beitragtimeformatid"] = $this->getBeitragTimeFormatID($session);
		$array["flags"] = array_flip($this->getFlags($session));
		$array["textfields"] = $this->getTextFields($session);
		return $array;
	}
}

?>
