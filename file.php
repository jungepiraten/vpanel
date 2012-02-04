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
	$dokumentid = $session->getIntVariable("dokumentid");
	$dokument = $session->getStorage()->getDokument($dokumentid);

	if (!$session->isAllowed("dokumente_show")) {
		die("<h1>403 Forbidden</h1>");
	}

	$file = $dokument->getFile();
} elseif ($session->hasVariable("fileid") && $session->hasVariable("token")) {
	$file = $session->getStorage()->getFile($session->getVariable("fileid"));
	$token = $session->getVariable("token");
	
	if (!$session->validToken("file" . $file->getFileID(), $token)) {
		die("<h1>403 Forbidden</h1>");
	}
}

switch ($session->hasVariable("mode") ? $session->getVariable("mode") : null) {
case "view":
	$token = $session->generateToken("file" . $file->getFileID());
	
	if (substr($file->getMimeType(),0,5) == "image") {
		$ui->viewFileImagePreview($file, $token);
		exit;
	}
	if ($file->getMimeType() == "application/pdf") {
		$fileparts = explode(".", $file->getAbsoluteFilename());
		array_pop($fileparts);
		$fileprefix = implode(".", $fileparts);
		$parts = glob($fileprefix . "-*.png");
		if (count($parts) == 0) {
			exec("convert " . escapeshellarg($file->getAbsoluteFilename()) . " " . escapeshellarg($fileprefix . ".png"));
		}
		$parts = glob($fileprefix . "-*.png");
		$ui->viewFilePDFPreview($file, $token, count($parts));
		exit;
	}
case "getpart":
	if ($file->getMimeType() == "application/pdf") {
		$fileparts = explode(".", $file->getAbsoluteFilename());
		array_pop($fileparts);
		$fileprefix = implode(".", $fileparts);

		header("Content-Type: image/png");
		readfile($fileprefix . "-" . $session->getVariable("part") . ".png");
		exit;
	}
case "get":
	if (!isset($file)) {
		die("<h1>404 Not Found</h1>");
	}

	header("Content-Type: " . $file->getMimeType());
	header("Content-Disposition: attachment; filename=\"" . addcslashes($file->getExportFilename(), '"') . "\"");

	readfile($file->getAbsoluteFilename());
	exit;
}

?>
