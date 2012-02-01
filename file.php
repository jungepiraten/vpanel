<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

if (!$session->isAllowed("mitglieder_show")) {
	$ui->viewLogin();
	exit;
}

switch ($session->hasVariable("mode") ? $session->getVariable("mode") : null) {
case "get":
	if ($session->hasVariable("tempfileid")) {
		$tempfileid = $session->getIntVariable("tempfileid");
		$tempfile = $session->getStorage()->getTempFile($tempfileid);

		if (!$tempfile->isAllowed($session->getUser())) {
			die("<h1>403 Forbidden</h1>");
		}

		$file = $tempfile->getFile();
	} elseif ($session->hasVariable("dokumentid")) {
		$dokumentid = $session->getIntVariable("dokumentid");
		$dokument = $session->getStorage()->getDokument($dokumentid);

		if (!$session->isAllowed("dokumente_show")) {
			die("<h1>403 Forbidden</h1>");
		}

		$file = $dokument->getFile();
	}

	header("Content-Type: " . $file->getMimeType());
	header("Content-Disposition: attachment; filename=\"" . addcslashes($file->getExportFilename(), '"') . "\"");

	readfile($file->getAbsoluteFilename());
	exit;
}

?>
