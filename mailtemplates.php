<?php

require_once(dirname(__FILE__) . "/config.inc.php");

require_once(VPANEL_UI . "/session.class.php");
$session = $config->getSession();
$ui = $session->getTemplate();

if (!$session->isSignedIn()) {
	$ui->viewLogin();
	exit;
}

require_once(VPANEL_CORE . "/mailtemplate.class.php");

function parseMailTemplateFormular($ui, $session, &$template = null) {
	$label = stripslashes($_POST["label"]);
	$body = stripslashes($_POST["body"]);

	if ($template == null) {
		$gliederung = $session->getStorage()->getGliederung($session->getVariable("gliederungid"));
		if (!$session->isAllowed("mailtemplates_create", $gliederung->getGliederungID())) {
			$ui->viewLogin();
			exit;
		}

		$template = new MailTemplate($session->getStorage());
		$template->setGliederung($gliederung);
	} else {
		$gliederung = $template->getGliederung();

		if (!$session->isAllowed("mailtemplates_modify", $gliederung->getGliederungID())) {
			$ui->viewLogin();
			exit;
		}
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
	$template = $session->getStorage()->getMailTemplate($session->getIntVariable("templateid"));
	if (!$session->isAllowed("mailtemplates_modify", $template->getGliederungID())) {
		$ui->viewLogin();
		exit;
	}

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
	if (!$session->isAllowed("mailtemplates_modify", $template->getGliederungID())) {
		$ui->viewLogin();
		exit;
	}
	
	$template->delAttachment($session->getIntVariable("fileid"));
	$template->save();
	
	$ui->redirect();
	exit;
case "details":
	$templateid = $session->getIntVariable("templateid");
	$template = $session->getStorage()->getMailTemplate($templateid);

	if ($session->getBoolVariable("save")) {
		parseMailTemplateFormular($ui, $session, $template);

		//$ui->redirect($session->getLink("mailtemplates_details", $template->getTemplateID()));
	}

	$ui->viewMailTemplateDetails($template);
	exit;
case "create":
	$gliederung = null;
	if ($session->hasVariable("gliederungid")) {
		$gliederung = $session->getStorage()->getGliederung($session->getVariable("gliederungid"));
	}

	if ($session->getBoolVariable("save")) {
		parseMailTemplateFormular($ui, $session, &$template);

		$ui->redirect($session->getLink("mailtemplates_details", $template->getTemplateID()));
	}

	$gliederungen = $session->getStorage()->getGliederungList($session->getAllowedGliederungIDs("mailtemplates_create"));
	$ui->viewMailTemplateCreate($gliederungen, $gliederung);
	exit;
case "delete":
	$template = $session->getStorage()->getMailTemplate($session->getIntVariable("templateid"));
	if (!$session->isAllowed("mailtemplates_delete", $template->getGliederungID())) {
		$ui->viewLogin();
		exit;
	}
	$template->delete();
	$ui->redirect($session->getLink("mailtemplates"));
	exit;
default:
	$gliederung = null;
	if ($session->hasVariable("gliederungid")) {
		if (!$session->isAllowed("mailtemplates_show", $session->getVariable("gliederungid"))) {
			$ui->viewLogin();
			exit;
		}

		$gliederungid = $session->getVariable("gliederungid");
		$gliederung = $session->getStorage()->getGliederung($gliederungid);
	} else {
		$gliederungid = $session->getAllowedGliederungIDs("mailtemplates_show");
	}

	$templates = $session->getStorage()->getMailTemplateList($gliederungid);
	$gliederungen = $session->getStorage()->getGliederungList($session->getAllowedGliederungIDs("mailtemplates_show"));
	$ui->viewMailTemplateList($templates, $gliederungen, $gliederung);
	exit;
}

?>
