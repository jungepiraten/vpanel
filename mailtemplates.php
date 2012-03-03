<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

if (!$session->isSignedIn()) {
	$ui->viewLogin();
	exit;
}

if (!$session->isAllowed("mailtemplates_show")) {
	$ui->viewLogin();
	exit;
}

require_once(VPANEL_CORE . "/mailtemplate.class.php");

function parseMailTemplateFormular($session, &$template = null) {
	$label = stripslashes($_POST["label"]);
	$body = stripslashes($_POST["body"]);

	if ($template == null) {
		$template = new MailTemplate($session->getStorage());
	}
	$template->setLabel($label);
	$template->setBody($body);
	
	// Headerfelder
	$headerfields = $session->getListVariable("headerfields");
	$headervalues = $session->getListVariable("headervalues");
	$headerfieldsindex = array_map('strtolower', $headerfields);
	foreach ($template->getHeaders() as $field => $header) {
		if (empty($field) || !in_array(strtolower($field), $headerfieldsindex)) {
			$template->delHeader($field);
		}
	}
	$headers = array_combine($headerfields, $headervalues);
	foreach ($headers as $field => $value) {
		if (!empty($field)) {
			$template->setHeader($field, $value);
		}
	}

	$template->save();
}

switch ($session->hasVariable("mode") ? $session->getVariable("mode") : null) {
case "createattachment":
	if (!$session->isAllowed("mailtemplates_modify")) {
		$ui->viewLogin();
		exit;
	}
	$template = $session->getStorage()->getMailTemplate($session->getIntVariable("templateid"));

	if ($session->getBoolVariable("save")) {
		$file = $session->getFileVariable("attachment");
		if ($file != null) {
			$template->addAttachment($file);
			$template->save();
		}
		
		$ui->redirect();
	}
	
	$ui->viewMailTemplateCreateAttachment($template);
	exit;
case "deleteattachment":
	$template = $session->getStorage()->getMailTemplate($session->getIntVariable("templateid"));
	
	$template->delAttachment($session->getIntVariable("fileid"));
	$template->save();
	
	$ui->redirect();
	exit;
case "details":
	$templateid = $session->getIntVariable("templateid");
	$template = $session->getStorage()->getMailTemplate($templateid);

	if ($session->getBoolVariable("save")) {
		if (!$session->isAllowed("mailtemplates_modify")) {
			$ui->viewLogin();
			exit;
		}

		parseMailTemplateFormular($session, $template);

		//$ui->redirect($session->getLink("mailtemplates_details", $template->getTemplateID()));
	}

	$ui->viewMailTemplateDetails($template);
	exit;
case "create":
	if ($session->getBoolVariable("save")) {
		if (!$session->isAllowed("mailtemplates_create")) {
			$ui->viewLogin();
			exit;
		}

		parseMailTemplateFormular($session, &$template);

		$ui->redirect($session->getLink("mailtemplates_details", $template->getTemplateID()));
	}

	$ui->viewMailTemplateCreate();
	exit;
case "delete":
	if (!$session->isAllowed("mailtemplates_delete")) {
		$ui->viewLogin();
		exit;
	}
	$templateid = $session->getIntVariable("templateid");
	$session->getStorage()->delMailTemplate($templateid);

	$ui->redirect($session->getLink("mailtemplates"));
	exit;
default:
	$templates = $session->getStorage()->getMailTemplateList();
	$ui->viewMailTemplateList($templates);
	exit;
}

?>
