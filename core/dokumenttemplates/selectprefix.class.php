<?php

require_once(VPANEL_DOKUMENTTEMPLATES . "/identifier.class.php");

class SelectPrefixDokumentTemplate extends IdentifierDokumentTemplate {
	private $prefixes;

	public function __construct($templateid, $label, $permission, $gliederungid, $prefixes, $identifierNumberLength = 3) {
		parent::__construct($templateid, $label, $permission, $gliederungid, $identifierNumberLength);
		$this->prefixes = $prefixes;
	}

	public function getPrefixOptions() {
		return array_map(create_function('$a', 'return $a["label"];'), $this->prefixes);
	}

	protected function getSelectedOption($session, $value) {
		return $this->prefixes[$session->getVariable("option")][$value];
	}

	protected function getIdentifierPrefix($session) {
		return $this->getSelectedOption($session, "prefix");
	}

	public function getDokumentKategorieID($session) {
		return $this->getSelectedOption($session, "kategorieid");
	}

	public function getDokumentStatusID($session) {
		return $this->getSelectedOption($session, "statusid");
	}

	public function getDokumentFlags($session) {
		return $this->getSelectedOption($session, "flags");
	}

	public function getDokumentLabel($session) {
		return $this->getSelectedOption($session, "labelprefix") . $session->getVariable("label");
	}

	public function getDokumentFile($session) {
		return $session->getFileVariable("file");
	}

	public function getDokumentData($session) {
		return array();
	}

	public function getDokumentKommentar($session) {
		return $session->getVariable("kommentar");
	}
}

?>
