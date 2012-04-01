<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

if (!$session->isAllowed("mitglieder_show")) {
	$ui->viewLogin();
	exit;
}

if ($session->hasVariable("tempfileid")) {
	$tempfileid = $session->getIntVariable("tempfileid");
	$tempfile = $session->getStorage()->getTempFile($tempfileid);

	if (!$tempfile->isAllowed($session->getUser())) {
		die("<h1>403 Forbidden</h1>");
	}

	$file = $tempfile->getFile();
} elseif ($session->hasVariable("dokumentid")) {
	$dokument = $session->getStorage()->getDokument($session->getIntVariable("dokumentid"));

	if (!$session->isAllowed("dokumente_show", $dokument->getGliederungID())) {
		die("<h1>403 Forbidden</h1>");
	}

	$file = $dokument->getFile();
} elseif ($session->hasVariable("mailtemplateid") && $session->hasVariable("fileid")) {
	if (!$session->isAllowed("mailtemplates_show")) {
		die("<h1>403 Forbidden</h1>");
	}

	$file = $session->getStorage()->getMailTemplate($session->getVariable("mailtemplateid"))->getAttachment($session->getVariable("fileid"));
} elseif ($session->hasVariable("fileid") && $session->hasVariable("token")) {
	$file = $session->getStorage()->getFile($session->getVariable("fileid"));
	$token = $session->getVariable("token");
	
	if (!$session->validToken("file" . $file->getFileID(), $token)) {
		die("<h1>403 Forbidden</h1>");
	}
} elseif ($session->hasVariable("statistikid") && $session->hasVariable("part")) {
	$statistik = $session->getStorage()->getMitgliederStatistik($session->getVariable("statistikid"));

	if ($statistik->getUserID() != $session->getUser()->getUserID()) {
		die("<h1>403 Forbidden</h1>");
	}
	switch ($session->getVariable("part")) {
	case "agegraph":
		$file = $statistik->getAgeGraphFile();
		break;
	case "timegraph":
		$file = $statistik->getTimeGraphFile();
		break;
	case "timebalancegraph":
		$file = $statistik->getTimeBalanceGraphFile();
		break;
	case "gliederungchart":
		$file = $statistik->getGliederungChartFile();
		break;
	case "statechart":
		$file = $statistik->getStateChartFile();
		break;
	case "mitgliedschaftchart":
		$file = $statistik->getMitgliedschaftChartFile();
		break;
	}
}

switch ($session->hasVariable("mode") ? $session->getVariable("mode") : null) {
case "view":
	$token = $session->generateToken("file" . $file->getFileID());
	
	if (substr($file->getMimeType(),0,5) == "image") {
		$ui->viewFileImagePreview($file, $token);
		exit;
	}
	if (substr($file->getMimeType(),0,4) == "text") {
		$ui->viewFileTextPreview($file, $token);
		exit;
	}
	if ($file->getMimeType() == "application/pdf") {
		$ui->viewFilePDFPreview($file, $token);
		exit;
	}
case "get":
	if (!isset($file) || !file_exists($file->getAbsoluteFilename())) {
		die("<h1>404 Not Found</h1>");
	}

	header("Content-Type: " . $file->getMimeType());
	header("Content-Disposition: inline; filename=\"" . addcslashes($file->getExportFilename(), '"') . "\"");

	readfile($file->getAbsoluteFilename());
	exit;
}

?>
