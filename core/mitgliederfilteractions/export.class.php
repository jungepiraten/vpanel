<?php

require_once(VPANEL_CORE . "/mitgliederfilteraction.class.php");
require_once(VPANEL_PROCESSES . "/mitgliederfilterexport.class.php");

class ExportMitgliederFilterAction extends MitgliederFilterAction {
	private $streamhandler;

	public function __construct($actionid, $label, $permission, $streamhandler) {
		parent::__construct($actionid, $label, $permission);
		$this->streamhandler = $streamhandler;
	}

	private function getStreamHandler($session) {
		return $this->streamhandler;
	}

	private function getPredefinedFields() {
		return array(
			array("label" => "Mitgliedsnummer",	"template" => "{MITGLIEDID}"),
			array("label" => "Bezeichnung",		"template" => "{BEZEICHNUNG}"),
			array("label" => "Vorname",		"template" => "{VORNAME}"),
			array("label" => "Nachname",		"template" => "{NAME}"),
			array("label" => "Firma",		"template" => "{FIRMA}"),
			array("label" => "Anschrift",		"template" => "{STRASSE} {HAUSNUMMER}"),
			array("label" => "Adresszusatz",	"template" => "{ADRESSZUSATZ}"),
			array("label" => "PLZ",			"template" => "{PLZ}"),
			array("label" => "Ort",			"template" => "{ORT}"),
			array("label" => "Bundesland",		"template" => "{STATE}"),
			array("label" => "Telefonnummer",	"template" => "{TELEFONNUMMER}"),
			array("label" => "Handynummer",		"template" => "{HANDYNUMMER}"),
			array("label" => "E-Mail",		"template" => "{EMAIL}"),
			array("label" => "Beitrag",		"template" => "{BEITRAG}"),
			array("label" => "Gliederung",		"template" => "{GLIEDERUNG}"),
			array("label" => "Mitgliedschaft",	"template" => "{MITGLIEDSCHAFT}")
		);
	}

	public function execute($config, $session, $filter, $matcher) {
		switch ($session->getVariable("export")) {
		default:
		case "options":
			return array("export" => "options", "predefinedfields" => $this->getPredefinedFields());
		case "export":
			// Headerfelder
			$exportfields = array();
			$exportfieldfields = $session->getListVariable("exportfields");
			$exportfieldvalues = $session->getListVariable("exportvalues");
			$exportfields = array_combine($exportfieldfields, $exportfieldvalues);
			unset($exportfields[""]);

			$process = new MitgliederFilterExportProcess($session->getStorage());
			$process->setStreamHandler($this->getStreamHandler($session));
			$process->setFields($exportfields);
			return $this->executeProcess($session, $process, $filter, $matcher);
		}
	}

	public function show($config, $session, $process) {
		if ($process->getStreamHandler() instanceof TempFileStreamHandler) {
			return array("redirect" => $session->getLink("tempfile_get", $process->getStreamHandler()->getTempFile()->getTempFileID()));
		}
	}
}

?>
