<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

if (!$session->isAllowed("mailtemplates_show")) {
	$ui->viewLogin();
	exit;
}

$attachment = $session->getStorage()->getMailAttachment($session->getVariable("attachmentid"));

header("Content-Type: " . $attachment->getMimeType() . "; filename=" . $attachent->getFilename());
print($attachment->getContent());

?>
