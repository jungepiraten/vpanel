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
	$fileid = intval($session->getVariable("fileid"));
	$file = $session->getStorage()->getFile($fileid);

	if (!$file->isAllowed($session->getUser())) {
		die("<h1>403 Forbidden</h1>");
	}

	header("Content-Type: " . $file->getMimeType());
	header("Content-Disposition: attachment; filename=\"" . addcslashes($file->getExportFilename(), '"') . "\"");

	readfile($file->getFileName());
	exit;
}

?>
