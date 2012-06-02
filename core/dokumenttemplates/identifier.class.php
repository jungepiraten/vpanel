<?php

require_once(VPANEL_CORE . "/dokumenttemplate.class.php");

abstract class IdentifierDokumentTemplate extends DokumentTemplate {
	private $identifierNumberLength;

	public function __construct($templateid, $label, $permission, $gliederungid, $identifierNumberLength = 3) {
		parent::__construct($templateid, $label, $permission, $gliederungid);
		$this->identifierNumberLength = $identifierNumberLength;
	}

	abstract protected function getIdentifierPrefix($session);

	public final function getDokumentIdentifier($session) {
		$identifierPrefix = $this->getIdentifierPrefix($session);
		$nextI = "";
		do {
			$i = $nextI;
			$number = $session->getStorage()->getDokumentIdentifierMaxNumber($identifierPrefix . $i, $this->identifierNumberLength) + 1;
			$nextI = intval($i) + 1;
		} while (strlen($number) > $this->identifierNumberLength);
		return $identifierPrefix . $i . str_pad($number, $this->identifierNumberLength, "0", STR_PAD_LEFT);
	}
}

?>
