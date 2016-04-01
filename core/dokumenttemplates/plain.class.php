<?php

require_once(VPANEL_CORE . "/dokumenttemplate.class.php");

class PlainDokumentTemplate extends DokumentTemplate {
	public function __construct($templateid, $label, $permission, $gliederungid) {
		parent::__construct($templateid, $label, $permission, $gliederungid);
	}

	public function getDokumentKategorieID($session) {
		return $session->getVariable("kategorieid");
	}

	public function getDokumentStatusID($session) {
		return $session->getVariable("statusid");
	}

	public function getDokumentIdentifier($session) {
		return $session->getVariable("identifier");
	}

	public function getDokumentFlags($session) {
		if ($session->hasVariable("flags")) {
			return $session->getRawVariable("flags");
		}
		return array();
	}

	public function getDokumentLabel($session) {
		return $session->getVariable("label");
	}

	public function getDokumentFile($session) {
		return $session->getFileVariable("file");
	}

	public function getDokumentData($session) {
		if ($session->hasVariable("data")) {
			return $session->getRawVariable("data");
		}
		return array();
	}

	public function getDokumentKommentar($session) {
		return $session->getVariable("kommentar");
	}
}

?>
